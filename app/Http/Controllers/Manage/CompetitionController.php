<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        return Inertia::render('Manage/Competitions', [
            'countries' => Country::all(),
            'competitionTypes' => GameType::all(),
            'competitions' => Competition::all()
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
        $token = Str::random(12);
        $gameType = GameType::where('id', $request->competition_type_id)->first();
        Competition::create([
            ...$request->all(),
            'token' => $token,
            'status' => 0,
            'is_cancelled' => 0,
            'language' => $gameType->language,
            'is_language_secondary_enabled' => $gameType->is_language_secondary_enabled,
            'language_secondary' => $gameType->language_secondary,
        ]);
        return redirect()->back();
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
