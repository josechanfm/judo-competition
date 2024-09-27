<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    //

    public function index(Competition $competition)
    {
        $competition->athletes;
        $programs = $competition->programs;
        $teams = $competition->teams;
        return Inertia::render('Manage/Teams', [
            'competition' => $competition,
            'programs' => $programs,
            'teams' => $teams
        ]);
    }

    public function store(Competition $competition, Request $request)
    {
        $validated = $request->validate([
            'abbreviation' => 'required|unique:teams,abbreviation,' . $competition->id,
            'name' => 'required',
            'name_secondary' => '',
            'leader' => '',
        ]);

        Team::create(['competition_id' => $competition->id, ...$validated]);
    }

    public function update(Competition $competition, Team $team, Request $request)
    {
        $validated = $request->validate([
            'abbreviation' => 'required|unique:teams,abbreviation,' . $competition->id,
            'name' => 'required',
            'name_secondary' => '',
            'leader' => '',
        ]);

        $team->update($validated);
    }

    public function destroy(){
        
    }
}
