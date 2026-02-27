<?php

namespace App\Http\Controllers\Manage;

use App\Exports\AthleteIDCardExport;
use App\Imports\NameSecondaryImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AthletesImport;
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
use App\Services\Printer\AthletePdfService;
use App\Services\Printer\AthleteWeighInService;
use App\Services\Printer\TeamAthletesService;
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
        $programs = $competition->programs;
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

        foreach ($validated['programs'] as $p) {
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
        // $import->failures()
        $import->import(request()->file('file'));
        // dd($import->failures());
        // dd('aaaa');
        return response()->json([
            'errors' => []
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
            // TODO: add filtering
            'name_display' => '',
            'gender' => 'required',
            'programs' => '',
            'new_team' => '',
            'team' => '',
            'team.*.name' => 'unique:teams',
            'team_id' => '',
        ]);

        if ($validated['new_team'] == true) {
            $team = Team::create(['name' => $validated['team'], 'abbreviation' => $validated['team'], 'competition_id' => $competition->id]);
            $validated['team_id'] = $team->id;
        }

        foreach ($validated['programs'] as $p) {
            ProgramAthlete::Create(['program_id' => $p, 'athlete_id' => $athlete->id]);
        }

        $athlete->update($validated);
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
            'programs'=>$competition->programs()->with('programAthletes')->get(),
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

    public function resetBoutQuence(Competition $competition){

        $service = (new BoutGenerationService($competition));

        $service->resetSequenceAndQueue();

        $service->invalidateByeBouts();
        // dd('aaaa');
        $service->resequence();
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
        
        $pdf = $TeamAthletesService->generateAllTeamsAthletesStatistics($competition,$teams);

        return response($pdf->Output("{$competition->name}運動員統計表.pdf", 'I'))
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

    public function sendAthletesCardEmail(Competition $competition)
    {
        // 獲取所有符合條件的 programAthletes
        $programAthletes = $competition->programAthletes()
            ->with(['athlete', 'program'])
            ->where('is_weight_passed', 1)
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
            $pdf = $service->generateOneIdCard($programAthlete);
            $path = 'public/pdf/athlte_cards/' . $programAthlete->name . $programAthlete->name_secondary . '.pdf';
            Storage::put($path, $pdf);
            // 準備郵件數據
            $mailData = [
                'title' => '恭喜你已成功報名',
                'subject' => '比賽報名表',
                'view_name' => 'mail.applicationMail',
                'file_path' => $path,
            ];
            
            // 獲取運動員郵箱
            $email = $programAthlete->email ?? null;

            if ($email) {
                Mail::to($email)->send(new TestMail($mailData));
                
                \Log::info('運動員證件郵件發送成功', [
                    'email' => $email, 
                    'athlete_id' => $programAthlete->id,
                    'program_athlete_id' => $programAthlete->id
                ]);
                $successCount++;
            } else {
                \Log::warning('運動員郵件地址為空', [
                    'athlete_id' => $programAthlete->id,
                    'program_athlete_id' => $programAthlete->id
                ]);
                $failCount++;
                $failedEmails[] = [
                    'athlete_id' => $programAthlete->id,
                    'name' => $programAthlete->name . $programAthlete->name_secondary,
                    'reason' => '郵箱為空'
                ];
            }

        }
        
        // 記錄最終結果
        \Log::info('批量發送運動員證件完成', [
            'competition_id' => $competition->id,
            'total' => $programAthletes->count(),
            'success' => $successCount,
            'fail' => $failCount,
            'failed_details' => $failedEmails
        ]);
        
        // 返回結果訊息
        $message = "成功發送 {$successCount} 個運動員證件";
        if ($failCount > 0) {
            $message .= "，{$failCount} 個發送失敗";
            return redirect()->back()->with('warning', $message);
        }
        
        return redirect()->back()->with('success', $message);
            
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
            $pdf = $service->generateOneIdCard($programAthlete);
            $path = 'public/pdf/athlte_cards/' . $programAthlete->name . $programAthlete->name_secondary . '.pdf';
            Storage::put($path, $pdf);
        }
    }
}

