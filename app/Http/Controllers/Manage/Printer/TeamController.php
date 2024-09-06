<?php

namespace App\Http\Controllers\manage\Printer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competition;
use App\Models\Team;
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
            'abbreviation' => 'required',
            'name' => 'required',
            'name_secondary' => '',
            'leader' => '',
        ]);

        Team::Create([...$validated, 'competition_id' => $competition->id]);

        return redirect()->back();
    }

    public function update(Competition $competition, Team $team, Request $request)
    {
        $validated = $request->validate([
            'abbreviation' => 'required',
            'name' => 'required',
            'name_secondary' => '',
            'leader' => '',
        ]);

        $team->update($validated);

        return redirect()->back();
    }
}
