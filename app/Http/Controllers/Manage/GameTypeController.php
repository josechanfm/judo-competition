<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Config;
use App\Models\GameCategory;
use App\Models\GameType;
use App\Utils\WeightUtil;

class GameTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Manage/GameTypes', [
            'gameTypes' => GameType::with('categories')->get(),
            'gameCategories' => GameCategory::all(),
            'languages' => Config::item('languages')
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
    public function update(Request $request, string $id)
    {
        $gameType = GameType::where('id', $id)->first();

        $gameType->name = $request->name;
        $gameType->name_secondary = $request->name_secondary;
        $gameType->code = $request->code;
        $gameType->winner_plus = $request->winner_plus;
        $gameType->language = $request->language;
        $gameType->is_language_secondary_enabled = $request->is_language_secondary_enabled;
        $gameType->language_secondary = $request->language_secondary;

        $gameType->save();

        collect($request->categories)->map(function ($category) use ($gameType) {
            // if > 1,000,000, then it is a timestamp placeholder, not an actual id
            $gameType->categories()->updateOrCreate(
                [
                    'id' => $category['id'] ?? null,
                ],
                [
                    'game_type_id' => $category['game_type_id'],
                    'name' => $category['name'],
                    'name_secondary' => $category['name_secondary'],
                    'code' => $category['code'],
                    'weights' => WeightUtil::sortWeights($category['weights']),
                    'duration' => $category['editDuration'],
                ]
            );
        });
        //
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
