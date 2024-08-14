<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Imports\AthletesImport;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\ProgramAthlete;
use App\Models\Program;
use App\Models\Team;
use App\Services\BoutGenerationService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;
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
        $validated = $request->validate([
            'name_zh' => 'required',
            // TODO: add filtering
            'name_pt' => '',
            'name_display' => '',
            'gender' => 'required',
            'team_id' => 'required',
        ]);

        Athlete::Create([...$validated, 'competition_id' => $competition->id]);

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

        $import->import(request()->file('file'));
        // dd($import->failures());

        return response()->json([
            'errors' => $import->failures()
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
            'name_zh' => 'required',
            // TODO: add filtering
            'name_pt' => '',
            'name_display' => '',
            'gender' => 'required',
            'team_id' => 'required',
        ]);

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
            'competition' => fn () => $competition,
            'programs' => $competition->programs()->get(),
        ]);
    }

    public function drawScreen(Competition $competition)
    {
        return Inertia::render('Draw/DrawScreen', [
            'competition' => fn () => $competition,
            'draw' => [
                'cover' => $competition->getDrawCoverUrlAttribute(),
                'background' => $competition->getDrawBackgroundUrlAttribute(),
            ]
        ]);
    }

    public function Weights(Request $request, Competition $competition)
    {
        // dd($competition->days);
        $competition->categories;
        $competition->programs;
        $competition->programsAthletes;
        return Inertia::render('Manage/Weights', [
            'competition' => $competition,
            // 'programs' => $competition->programs,
            // 'programsAthletes'=>$competition->programsAthletes,
            // 'programsAthletes' => QueryBuilder::for($competition->programsAthletes())
            //     ->allowedFilters([
            //         AllowedFilter::exact('category', 'program.category_id'),
            //         AllowedFilter::exact('weight_code', 'program.weight_code'),
            //         AllowedFilter::exact('date', 'program.date')->default($competition?->days[0] ?? ''),
            //         AllowedFilter::custom('name', new class implements Filter
            //         {
            //             public function __invoke($query, $value, string $property)
            //             {
            //                 $query->whereHas('athlete', function ($query) use ($value) {
            //                     $query->where('name', 'like', "%$value%")
            //                         ->orWhere('name_secondary', 'like', "%$value%")
            //                         ->orWhere('id', $value);
            //                 });
            //             }
            //         }),
            //     ])
            //     ->with(['athlete.team', 'program'])
            //     ->paginate(
            //         $request->input('per_page', 10)
            //     ),
            // 'athletes' => QueryBuilder::for($competition->programsAthletes())->allowedFilters([
            //     AllowedFilter::exact('category', 'program.category_id'),
            //     AllowedFilter::exact('weight_code', 'program.weight_code'),
            //     AllowedFilter::exact('date', 'program.date')->default($competition->days[0])
            // ])->get(),
            'categories' => $competition->programs->unique(fn ($program) => $program->competitionCategory->id)->pluck('competitionCategory'),
            'weights' => $competition->programs->unique(fn ($program) => $program->weight_code)->pluck('weight_code'),
        ]);
    }
    public function pass(Request $request, Competition $competition, ProgramAthlete $programAthlete)
    {
        $programAthlete->weight = $request->weight;
        $programAthlete->is_weight_passed = 1;
        // $programAthlete->confirmed = true;
        $programAthlete->save();

        return redirect()->back();
    }

    public function fail(Request $request, Competition $competition, ProgramAthlete $programAthlete)
    {

        $programAthlete->weight = $request->weight; 
        $programAthlete->is_weight_passed = 0;
        // $programAthlete->confirmed = true;
        $programAthlete->save();

        return redirect()->back();
    }

    public function Weightslock(Competition $competition, Request $request)
    {
        // TODO: check whether all athletes have weight-in
        if ($competition->programsAthletes()->where('is_weight_passed', null)->whereHas('program', function ($query) use ($request) {
            return $query->where('date', $request->date);
        })->exists()) {
            abort(409, 'Not all athletes have confirm');
        } else {
            $competition->programsAthletes()->whereHas('program', function ($query) use ($request) {
                return $query->where('date', $request->date);
            })->update(['confirm' => 1]);
        }
        // return false;
        // call reflow sequence
        $service = (new BoutGenerationService($competition));
        // dd($service);
        $service->invalidateWeightBouts($request->date);
        $service->invalidateByeBouts($request->date, 1);
        $service->resequence($request->date);

        $competition->update(['status' => 4]);
        return redirect()->back();
    }
}
