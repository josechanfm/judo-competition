<?php

namespace App\Http\Controllers\Manage;

use App\Exports\AthleteIDCardExport;
use App\Exports\WeightInExcelExport;
use App\Imports\NameSecondaryImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AthletesImport;
use App\Imports\WeightInImport;
use App\Jobs\SendAthleteCardJob;
use App\Mail\TestMail;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\ProgramAthlete;
use App\Models\Program;
use App\Models\Team;
use App\Services\BoutGenerationService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Services\Printer\AthleteCheckInService;
use App\Services\Printer\AthletePdfService;
use App\Services\Printer\AthleteWeighInService;
use App\Services\Printer\TeamAthletesService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;



class AthleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Competition $competition)
    {
        $competition->athletes;
        $programs = $competition->programs()->orderByCategoryAndWeightGroup()->get();
        $teams = $competition->teams;
        // dd($competition->programAthletes);
        return Inertia::render('Manage/Athletes', [
            'competition' => $competition,
            'programs' => $programs,
            'teams' => $teams
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Competition $competition, Request $request)
    {
        //
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required',
            'name_secondary' => '',
            'new_team' => '',
            'programs' => '',
            'team' => '',
            // TODO: add filtering
            'name_display' => '',
            'gender' => 'required',
            'team_id' => '',
        ]);

        if ($validated['new_team'] == true) {
            $team = Team::create(['name' => $validated['team'], 'abbreviation' => $validated['team'], 'competition_id' => $competition->id]);
            $validated['team_id'] = $team->id;
        }

        $athlete = Athlete::Create([...$validated, 'competition_id' => $competition->id]);

        foreach ($validated['programs'] ?? [] as $p) {
            ProgramAthlete::Create(['program_id' => $p, 'athlete_id' => $athlete->id]);
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    public function lock(Competition $competition)
    {
        $competition->programs()->doesntHave('athletes')
            ->orWhere(function ($query) {
                $query->has('athletes', '<', 2);
            })
            ->delete();

        $competition->programs->each(function (Program $program) {
            $program->setProgram();
        });

        $competition->update(['status' => 1]);

        return redirect()->back();
    }

    public function unlock(Competition $competition)
    {
        $competition->programs()->delete();

        foreach ($competition->categories as $category) {
            // dd($gc);
            $seq = 1;

            if ($competition->gender == 0) {
                $filtered = array_filter($category->weights, function ($weight) {
                    return strpos($weight, "FW") !== false;
                });
            } else if ($competition->gender == 1) {
                $filtered = array_filter($category->weights, function ($weight) {
                    return strpos($weight, "MW") !== false;
                });
            } else {
                $filtered = $category->weights;
            }

            foreach ($filtered as $w) {
                Program::create([
                    'competition_id' => $competition->id,
                    'competition_category_id' => $category->id,
                    'date' => $competition->days[0],
                    'mat' => 1,
                    'section' => 1,
                    'weight_code' => $w,
                    'sequence' => $seq,
                    'competition_system' => $competition->system == 'Q' ? 'erm' : ($competition->system == 'F' ? 'full' : 'kos'),
                    'duration' => $category->duration,
                    'chart_size' => 0,
                    'status' => 0,
                ]);
                $seq++;
            }
        }
        // dd($competition->categories);
        $competition->update(['status' => 0]);

        return redirect()->back();
    }
    public function import(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $competition->programs()->each(function ($program) {
            $program->athletes()->detach();
        });

        $competition->athletes()->delete();
        $competition->teams()->delete();
        // remove all athletes in programs

        $import = new AthletesImport($competition);
        $import->import(request()->file('file'));

        $errors = $import->failures()
            ->map(function ($failure) {
                return [
                    'row' => $failure->row(),
                    'errors' => $failure->errors(),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'errors' => $errors,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competition $competition, Athlete $athlete)
    {
        //
        // dd($request);
        $validated = $request->validate([
            'name' => 'required',
            'name_secondary' => '',
            // TODO: add filtering
            'name_display' => '',
            'gender' => 'required',
            'programs' => 'nullable|array',
            'programs.*' => 'integer',
            'new_team' => '',
            'team' => '',
            'team.*.name' => 'unique:teams',
            'team_id' => '',
        ]);

        if ($validated['new_team'] == true) {
            $team = Team::create(['name' => $validated['team'], 'abbreviation' => $validated['team'], 'competition_id' => $competition->id]);
            $validated['team_id'] = $team->id;
        }

        // 只接受這場賽事底下的項目，避免跨賽事指派
        $programIds = $competition->programs()
            ->whereIn('programs.id', $validated['programs'] ?? [])
            ->pluck('programs.id')
            ->all();

        // 用 sync 同步項目：移除的項目會被刪掉、新加入的會新增，
        // 保留的紀錄（座位、過磅、確認…）原封不動。
        // 原本是逐筆 ProgramAthlete::create()，所以移除項目不會生效、
        // 重複儲存還會產生重複的 program_athlete 紀錄。
        $athlete->programs()->sync($programIds);

        unset($validated['programs'], $validated['new_team'], $validated['team']);

        $athlete->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function drawControl(Competition $competition)
    {
        $competition->competition_type;
        return Inertia::render('Draw/DrawControl', [
            'competition' => fn() => $competition,
            'programs' => $competition->programs()->get(),
        ]);
    }

    public function drawScreen(Competition $competition)
    {
        $competition->competition_type;
        return Inertia::render('Draw/DrawScreen', [
            'competition' => fn() => $competition,
            'draw' => [
                'cover' => $competition->getDrawCoverUrlAttribute(),
                'background' => $competition->getDrawBackgroundUrlAttribute(),
            ]
        ]);
    }

    public function Weights(Request $request, Competition $competition)
    {
        $competition->categories;
        // dd($competition->programAthletes[0]);
        return Inertia::render('Manage/Weights', [
            'programs'=>$competition->programs()->with('programAthletes')->orderByCategoryAndWeightGroup()->get(),
            'competition' => $competition,
        ]);
    }

    public function weightChecked(Request $request, Competition $competition, ProgramAthlete $programAthlete)
    {
        // dd('aaa');
        $programAthlete->weight = $request->weight;

        $programAthlete->is_weight_passed = $request->is_weight_passed;

        $programAthlete->save();

        return redirect()->back();
    }


    public function weightsLock(Competition $competition, Request $request)
    {
        // dd($request->all());
        $program = Program::where('id', $request->program)->first();
        // TODO: check whether all athletes have weight-in
        if ($program->athletes()->where('is_weight_passed', null)->exists()) {
            abort(409, 'Not all athletes have confirm');
        } else {
            $program->athletes()->update(['confirm' => 1]);
        }
        // return false;
        // call reflow sequence
        $service = (new BoutGenerationService($competition));
        // dd($service);
        $service->invalidateWeightBouts($program->bouts);
        $service->weightByeBouts($program->bouts);
        $service->resequence();

        $competition->update(['status' => 4]);
        return redirect()->back();
    }

    public function weightsCancelLock(Competition $competition, Request $request)
    {

        $program = Program::where('id', $request->program)->first();

        $program->athletes()->update(['confirm' => 0]);

        $program->bouts()->update(['status' => 0, 'winner' => 0, 'queue' => 1]);

        $service = (new BoutGenerationService($competition));

        $service->weightByeBouts($program->bouts);
        $service->resequence();
        
        return redirect()->back();
    }

    /**
     * 重置過磅資料：只清空「體重」與「過磅結果」，不動鎖定(confirm)與場次。
     */
    private function clearWeighInData($query): void
    {
        $query->update([
            'weight' => null,
            'is_weight_passed' => null,
        ]);
    }

    // 單一選手
    public function resetWeightChecked(Competition $competition, ProgramAthlete $programAthlete)
    {
        $this->assertProgramBelongsToCompetition($competition, $programAthlete->program);

        $this->clearWeighInData(ProgramAthlete::whereKey($programAthlete->id));

        return redirect()->back();
    }

    // 單一項目
    public function resetProgramWeights(Competition $competition, Program $program)
    {
        $this->assertProgramBelongsToCompetition($competition, $program);

        $this->clearWeighInData($program->programAthletes());

        return redirect()->back();
    }

    // 整場賽事（所有項目）
    public function resetAllWeights(Competition $competition)
    {
        $programIds = $competition->programs()->pluck('programs.id');

        $this->clearWeighInData(ProgramAthlete::whereIn('program_id', $programIds));

        return redirect()->back();
    }

    private function assertProgramBelongsToCompetition(Competition $competition, ?Program $program): void
    {
        $category = $program?->competitionCategory;

        if (! $category || (int) $category->competition_id !== (int) $competition->id) {
            abort(404);
        }
    }

    public function resetBoutQuence(Competition $competition){

        $service = (new BoutGenerationService($competition));

        $service->resetSequenceAndQueue();

        $service->invalidateByeBouts();
        // dd('aaaa');
        $service->resequence();
    }

    /**
     * 匯出整場賽事的過磅資料（Excel），供工作人員填寫後再匯入。
     */
    public function exportWeightIn(Competition $competition)
    {
        $name = preg_replace('/[\/\\\\:*?"<>|]/', '-', (string) ($competition->name ?? 'competition'));

        return Excel::download(
            new WeightInExcelExport($competition),
            $name . '過磅資料_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * 匯入過磅資料（體重 + 過磅結果），只更新這兩個欄位。
     */
    public function importWeightIn(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $import = new WeightInImport($competition);

        Excel::import($import, $request->file('file'));

        return response()->json([
            'updated' => $import->updated,
            'errors' => $import->errors,
        ]);
    }
    
    public function generateIdCards(Competition $competition)
    {
        // 直接使用传入的 competition 实例，加载完整关系链
        $competition->load([
            'categories.programs.programAthletes.athlete'
        ]);

        $programAthletes = $competition->categories->flatMap(function ($category) {
            return $category->programs->flatMap(function ($program) use ($category) {
                return $program->programAthletes->map(function ($programAthlete) use ($program, $category) {
                    // 複製運動員對象
                    $athlete = clone $programAthlete->athlete;
                    
                    // 添加項目特定信息
                    $athlete->program_name = $program->name;
                    $athlete->programCategoryWeight = $program->convertGender() . $program->competitionCategory->name . $program->convertWeight();
                    $athlete->team = $athlete->team;
                    $athlete->original_program_id = $program->id; // 標記原始項目
                    
                    return $athlete;
                });
            });
        });
        // dd($programAthletes[0]);
        if ($programAthletes->isEmpty()) {
            return back()->with('error', '該項目沒有運動員');
        }

        $pdfService = new AthletePdfService();
        $pdfService->useCompetitionIdCardSettings($competition);
        $pdf = $pdfService->generateIdCard($programAthletes);

        return response($pdf->Output("{$competition->name}_id_cards.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function generateAllWeighInTable(Competition $competition)
    {
        $programs = $competition->programs()->with(['athletes.team'])->get();
        
        if ($programs->isEmpty()) {
            return response()->json(['error' => '沒有找到任何量級'], 404);
        }

        $weighInService = new AthleteWeighInService();
        // 可以自定義標題和logo
        $weighInService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $weighInService->setCompetitionLogo($competition);
        
        $pdf = $weighInService->generateAllWeighInTable($programs);

        // 輸出 PDF
        return response($pdf->Output("{$competition->name}過磅表.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function importExcel()
    {
        try {
            $filePath = public_path('name_secondary.xlsx');
            
            // 检查文件是否存在
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', '文件不存在：' . $filePath);
            }
            
            // 执行导入
            Excel::import(new NameSecondaryImport, $filePath);
            
            $result = session('import_result', '数据导入完成！');
            return redirect()->back()->with('success', $result);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '导入失败：' . $e->getMessage());
        }
    }

    public function generateAllTeamsAthletes(Competition $competition)
    {
        $teams = $competition->teams()->with(['athletes' => function ($query) {
            $query->orderBy('gender');
        }])->get();

        $TeamAthletesService = new TeamAthletesService();

        $TeamAthletesService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $TeamAthletesService->setCompetitionLogo($competition);
        
        $pdf = $TeamAthletesService->generateAllTeamsAthletes($competition,$teams);


        return response($pdf->Output("{$competition->name}運動員名單.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function generateAllTeamsAthletesStatistics(Competition $competition)
    {
        $teams = $competition->teams()
            ->with(['athletes', 'athletes.programs.category'])
            ->orderBy('abbreviation')
            ->get()->map(function ($team) {
                $data = [];
                foreach ($team->athletes as $athlete) {
                    // 确保 athlete 有 programs 且不为空
                    if ($athlete->programs && isset($athlete->programs[0])) {
                        $program = $athlete->programs[0];
                        if ($program->category) {
                            $code = $program->category->code;
                            $gender = $athlete->gender;
                            
                            if (!isset($data[$code][$gender])) {
                                $data[$code][$gender] = 1;
                            } else {
                                $data[$code][$gender]++;
                            }
                        }
                    }
                }
                $team->athletesCategoryCount = $data;
                $team->athletesCount = $team->athletes->count();
                return $team;
            });
        $TeamAthletesService = new TeamAthletesService();

        $TeamAthletesService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $TeamAthletesService->setCompetitionLogo($competition);
        
        $pdf = $TeamAthletesService->generateAllTeamsAthletesStatistics($competition,$teams);

        return response($pdf->Output("{$competition->name}運動員統計表.pdf", 'I'))
        ->header('Content-Type', 'application/pdf');
    }

    public function generateAllFailWeighInAthletes(Competition $competition)
    {
        $failAthletes = $competition->programAthletes()
            ->with('athlete', 'team', 'program')
            ->where('is_weight_passed', 0)
            ->where('weight', 0) 
            ->get()
            ->sortBy(function($item) {
                return $item->team->name ?? $item->team->id; // 按队伍名称或ID排序
            });

        $TeamAthletesService = new TeamAthletesService();

        $TeamAthletesService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $TeamAthletesService->setCompetitionLogo($competition);
        
        $pdf = $TeamAthletesService->generateAllFailWeighInAthletes($failAthletes);

        return response($pdf->Output("{$competition->name}過磅失敗表.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function generateAllTeamsAthletesResult(Competition $competition)
    {
        $competition->load('programs', 'competition_type', 'teams');
        $teams = $competition->teams()->with(['athletes', 'athletes.programs'])->orderBy('abbreviation')->get();
        $teams->map(function ($team) {
            $gold = 0;
            $silver = 0;
            $bronze = 0;
            $total = 0;
            foreach ($team->athletes as $a) {
                $gold = $gold + $a->programAthletes->where('rank', 1)->where('abstain',null)->where('is_weight_passed',1)->count();
                $silver += $a->programAthletes->where('rank', 2)->where('abstain',null)->where('is_weight_passed',1)->count();
                $bronze += $a->programAthletes->where('rank', 3)->where('abstain',null)->where('is_weight_passed',1)->count();
            }
            $total  = $gold + $silver + $bronze;
            $a = (object)[
                'gold' => $gold,
                'silver' => $silver,
                'bronze' => $bronze,
                'total' => $total
            ];
            return $team->medals = $a;
        });
        $teams = $teams->sortByDesc(function ($t) {
            return $t->medals->bronze;
        })->sortByDesc(function ($t) {
            return $t->medals->silver;
        })->sortByDesc(function ($t) {
            return $t->medals->gold;
        })->values();
        // dd($teams[0]);
        $TeamAthletesService = new TeamAthletesService();

        $TeamAthletesService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $TeamAthletesService->setCompetitionLogo($competition);
        
        $pdf = $TeamAthletesService->generateAllTeamsAthletesResult($teams);

        return response($pdf->Output("{$competition->name}成績排名榜.pdf", 'I'))
        ->header('Content-Type', 'application/pdf');
    }

    public function exportAthletesIDCard(Competition $competition){
        $programAthletes = $competition->categories->flatMap(function ($category) {
            return $category->programs->flatMap(function ($program) use ($category) {
                return $program->programAthletes->map(function ($programAthlete) use ($program, $category) {
                    // 複製運動員對象
                    $athlete = clone $programAthlete->athlete;
                    
                    // 添加項目特定信息
                    $athlete->program_name = $program->name;
                    $athlete->programCategoryWeight = $program->convertGender() . $program->competitionCategory->name . $program->convertWeight();
                    $athlete->team = $athlete->team;
                    $athlete->original_program_id = $program->id; // 標記原始項目
                    
                    return $athlete;
                });
            });
        });

        $fileName = $competition->name . '運動員ID_Card表.xlsx';

        return Excel::download(new AthleteIDCardExport($programAthletes), $fileName);
    }

    /**
     * 運動員簽到表 PDF：每一個「日期 + 場地 + 時段」一頁，列出該時段要出賽的運動員。
     */
    public function generateAllCheckInAthletes(Competition $competition)
    {
        $matNumbers = range(1, $competition->mat_number);
        $sectionNumbers = range(1, $competition->section_number);

        $groups = [];

        foreach ($competition->days ?? [] as $day) {
            foreach ($matNumbers as $mat) {
                foreach ($sectionNumbers as $section) {
                    $programs = $competition->programs()
                        ->where('mat', $mat)
                        ->where('date', $day)
                        ->where('section', $section)
                        ->orderBy('sequence')
                        ->with(['competitionCategory', 'programAthletes.athlete.team'])
                        ->get()
                        // 只有「未過磅」或「過磅成功」的運動員要簽到
                        ->filter(fn ($program) => $program->programAthletes->contains(
                            fn ($programAthlete) => is_null($programAthlete->is_weight_passed)
                                || (int) $programAthlete->is_weight_passed === 1
                        ));

                    $groups[] = [
                        'date' => $day,
                        'mat' => $mat,
                        'section' => $section,
                        'programs' => $programs,
                    ];
                }
            }
        }

        $checkInService = new AthleteCheckInService();
        $checkInService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $checkInService->setCompetitionLogo($competition);

        $pdf = $checkInService->generate(collect($groups));

        return response($pdf->Output("{$competition->name}運動員簽到表.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function sendAthletesCardEmail(Competition $competition)
    {

        $programAthletes = $competition->programAthletes()
            ->with(['athlete', 'program'])
            ->where('is_weight_passed', 1)
            ->get()
            ->map(function ($programAthlete) {
                $athlete = (object) $programAthlete->athlete->toArray();
                
                // 添加項目特定信息
                $athlete->program_name = $programAthlete->program->name;
                $athlete->programCategoryWeight = $programAthlete->program->convertGender() 
                    . $programAthlete->program->competitionCategory->name 
                    . $programAthlete->program->convertWeight();
                $athlete->team = $athlete->team;
                $athlete->original_program_id = $programAthlete->program->id;
                $athlete->program_athlete_id = $programAthlete->id; // 保留原始 ID
                
                return $athlete;
            });
        // dd($programAthletes[0]);
        $totalCount = $programAthletes->count();
        
        if ($totalCount === 0) {
            return redirect()->back()->with('warning', '沒有符合條件的運動員');
        }
        
        // 分發任務到佇列
        foreach ($programAthletes as $athlete) {
            SendAthleteCardJob::dispatch($athlete, $competition);
        }
        
        // 立即返回，不用等待
        return redirect()->back()->with('success', "已將 {$totalCount} 個發送任務加入佇列，將在背景處理");

    }
    public function testA5athletesCard(Competition $competition){
         $programAthletes = $competition->programAthletes()
            ->with(['athlete', 'program'])
            ->whereHas('athlete', function($query) {
                $query->where('email', 'abc95175346@hotmail.com');
            })
            ->get()
            ->map(function ($programAthlete) {
                $athlete = clone $programAthlete->athlete;
                
                // 添加項目特定信息
                $athlete->program_name = $programAthlete->program->name;
                $athlete->programCategoryWeight = $programAthlete->program->convertGender() 
                    . $programAthlete->program->competitionCategory->name 
                    . $programAthlete->program->convertWeight();
                $athlete->team = $athlete->team;
                $athlete->original_program_id = $programAthlete->program->id; // 標記原始項目
                
                return $athlete;
            });
        
        $successCount = 0;
        $failCount = 0;
        $failedEmails = [];
        
        foreach ($programAthletes as $programAthlete) {
            // 使用 AthletePdfService 生成運動員證
            $service = new AthletePdfService();
            $service->useCompetitionIdCardSettings($competition);
            $pdf = $service->generateOneIdCard($programAthlete);
            $path = 'public/pdf/athlte_cards/' . $programAthlete->name . $programAthlete->name_secondary . '.pdf';
            Storage::put($path, $pdf);
        }
    }
}

