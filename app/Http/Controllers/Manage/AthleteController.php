<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
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
        // dd($competition->programsAthletes);
        return Inertia::render('Manage/Weights', [
            'programs_athletes' => $competition->programsAthletes,
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


    public function Weightslock(Competition $competition, Request $request)
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
}
