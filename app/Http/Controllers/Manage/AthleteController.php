<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\Team;
use Illuminate\Http\Request;
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
        $teams = Team::all();
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
    public function store(Request $request)
    {
        //
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
}
