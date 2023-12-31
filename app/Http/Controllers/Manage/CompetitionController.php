<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Competition;
use App\Models\GameType;
use App\Models\Country;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Manage/Competitions',[
            'countries'=>Country::all(),
            'competitionTypes'=>GameType::all(),
            'competitions'=>Competition::all()
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
        Competition::create($request->all());
        return response()->json($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competition $competition)
    {
        return response()->json($competition);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Competition $competition, Request $request)
    {
        $competition->update($request->all());
        return redirect()->back();
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
