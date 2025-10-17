<?php

namespace App\Http\Controllers\Manage;

use App\Imports\NameSecondaryImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AthletesImport;
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
        // dd($competition->programs);
        return Inertia::render('Draw/DrawControl', [
            'competition' => fn() => $competition,
            'programs' => $competition->programs()->get(),
        ]);
    }

    public function drawScreen(Competition $competition)
    {
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
        // dd($competition->programAthletes);
        return Inertia::render('Manage/Weights', [
            'programs_athletes' => $competition->programAthletes,
            'competition' => $competition,
        ]);
    }
    public function weightChecked(Request $request, Competition $competition, ProgramAthlete $programAthlete)
    {
        // dd('aaa');
        $programAthlete->weight = $request->weight;

        $programAthlete->is_weight_passed = 1;

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
                    $athlete->programCategoryWeight = $program->converGender() . $program->competitionCategory->name . $program->convertWeight();
                    $athlete->team = $athlete->team;
                    $athlete->original_program_id = $program->id; // 標記原始項目
                    
                    return $athlete;
                });
            });
        });
        // dd($programAthletes[0]);
        if ($programAthletes->isEmpty()) {
            return back()->with('error', '该比赛没有参赛运动员');
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
            $competition->name
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
}

