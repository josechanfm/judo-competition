<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\CompetitionType;
use App\Models\Config;
use App\Models\GameType;
use App\Models\Country;
use App\Models\GameCategory;
use App\Models\Program;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Manage/Competitions/Index', [
            'countries' => Country::all(),
            'gameTypes' => GameType::all(),
            'competitions' => Competition::with('competition_type')->get(),
            'languages' => Config::item('languages'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('Manage/Competitions/Create', [
            'countries' => Country::all(),
            'gameTypes' => GameType::with('categories')->get(),
            'languages' => Config::item('languages'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_type_id' => 'required',
            'name' => 'required',
            'country' => '',
            // TODO: add filtering
            'scale' => '',
            'system' => '',
            'small_system' => '',
            'competition_type' => '',
            'gender' => '',
            'seeding' => '',
            'name_secondary' => '',
            'type' => '',
            'categories' => '',
            'date_start' => 'required',
            'date_end' => 'required',
            'mat_number' => 'required',
            'section_number' => 'required',
            'days' => 'required',
            'remark' => '',
            'competition.name' => 'required_if:competition,true',
            'competition.language' => 'required_if:competition,true',
            'competition.is_language_secondary_enabled' => 'required_if:competition,true|boolean',
            'competition.name_secondary' => 'required_if:competition_is_language_secondary_enabled,true',
            'competition.language_secondary' => 'required_if:competition_is_language_secondary_enabled,true'
        ]);
        $token = Str::random(12);
        $competition_type = $validated['competition_type'];
        // dd(value_column($gameType));
        $competition = Competition::create([
            ...$validated,
            'token' => $token,
            'status' => 0,
            'is_cancelled' => 0,
        ]);
        CompetitionType::create([...$competition_type, 'competition_id' => $competition->id]);
        foreach ($validated['categories'] as $gc) {
            // dd($gc);
            $seq = 1;
            $competitionCategory = CompetitionCategory::create([...$gc, 'competition_id' => $competition->id]);

            if ($competition->gender == 0) {
                $filtered = array_filter($competitionCategory->weights, function ($weight) {
                    return strpos($weight, "FW") !== false;
                });
            } else if ($competition->gender == 1) {
                $filtered = array_filter($competitionCategory->weights, function ($weight) {
                    return strpos($weight, "MW") !== false;
                });
            } else {
                $filtered = $competitionCategory->weights;
            }

            foreach ($filtered as $w) {
                Program::create([
                    'competition_id' => $competition->id,
                    'competition_category_id' => $competitionCategory->id,
                    'date' => $competition->days[0],
                    'mat' => 1,
                    'section' => 1,
                    'weight_code' => $w,
                    'sequence' => $seq,
                    'competition_system' => $competition->system == 'Q' ? 'erm' : ($competition->system == 'F' ? 'full' : 'kos'),
                    'duration' => $competitionCategory->duration,
                    'chart_size' => 0,
                    'status' => 0,
                ]);
                $seq++;
            }
        }
        return redirect()->route('manage.competitions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competition $competition)
    {
        $competition->competition_type;
        // dd(CompetitionCategory::where('competition_id', $competition->id)->get());
        return Inertia::render('Manage/Competitions/Edit', [
            'competition' => $competition,
            'competition_categories' => CompetitionCategory::where('competition_id', $competition->id)->get(),
            'countries' => Country::all(),
            'gameTypes' => GameType::with('categories')->get(),
            'languages' => Config::item('languages'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Competition $competition, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'country' => '',
            // TODO: add filtering
            'scale' => '',
            'system' => '',
            'small_system' => '',
            'gender' => '',
            'seeding' => '',
            'name_secondary' => '',
            'type' => '',
            'date_start' => 'required',
            'date_end' => 'required',
            'mat_number' => 'required',
            'section_number' => 'required',
            'days' => 'required',
            'remark' => '',
            'categories' => '',
            'competition_type.name' => 'required_if:competition_type,true',
            'competition_type.language' => 'required_if:compcompetition_typeetition,true',
            'competition_type.is_language_secondary_enabled' => 'required_if:competition_type,true|boolean',
            'competition_type.name_secondary' => 'required_if:competition_type.is_language_secondary_enabled,true',
            'competition_type.language_secondary' => 'required_if:competition_type.is_language_secondary_enabled,true'
        ]);
        $competition->competition_type->update($validated['competition_type']);
        unset($validated['competition_type']);
        $competition->update($validated);

        return redirect()->route('manage.competitions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
