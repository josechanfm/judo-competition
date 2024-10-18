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
        $gameTypes = GameType::with('categories')->get();
        foreach ($gameTypes as $type) {
            foreach ($type->categories as $category) {
                $category->duration = str_pad(strval(intval(($category->duration ?? 0) / 60)), 2, '0', STR_PAD_LEFT) . ':' . str_pad(strval($category->duration % 60), 2, '0', STR_PAD_RIGHT);
            }
        };
        return Inertia::render('Manage/GameTypes', [
            'gameTypes' => $gameTypes,
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
        // dd($request->all());
        // dd($validated['is_language_secondary_enabled']);
        $validated = $request->validate([
            'id' => 'sometimes',
            'name' => 'required',
            'name_secondary' => 'required_if:is_language_secondary_enabled,true',
            // TODO: add filtering
            'awarding_methods' => 'required',
            'language' => 'required',
            'is_language_secondary_enabled' => 'required',
            'language_secondary' => 'required_if:is_language_secondary_enabled,true',
            'code' => 'required',
            'categories' => 'array',
            'categories.*.id' => 'sometimes',
            'categories.*.name' => 'required_if:categories,true',
            //            'categories.*.name_en' => 'required',
            'categories.*.name_secondary' => 'required_if:is_language_secondary_enabled,true',
            'categories.*.code' => 'required_if:categories,true',
            'categories.*.editDuration' => 'required_if:categories,true|integer',
            'categories.*.weights' => 'array|required_if:categories,true',
            'delete_categories' => 'array',
        ]);
        $gameType = GameType::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            [
                'name' => $validated['name'],
                //                'name_en' => $validated['name_en'],
                'name_secondary' => $validated['name_secondary'] ?? null,
                'language' => $validated['language'],
                'is_language_secondary_enabled' => $validated['is_language_secondary_enabled'] == true ? 1 : 0,
                'language_secondary' => $validated['language_secondary'] ?? null,
                'awarding_methods' => $validated['awarding_methods'],
                'code' => $validated['code'],
            ]
        );

        collect($request->categories)->map(function ($category) use ($gameType) {
            // if > 1,000,000, then it is a timestamp placeholder, not an actual id
            $gameType->categories()->updateOrCreate(
                [
                    'id' => $category['id'] < 10000000 ? $category['id'] : null,
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
        collect($request->removeCategories)->map(function ($category) {
            GameCategory::where('id', $category['id'])->delete();
        });
        // dd($request->removeCategories);
        //
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $game_categories = GameCategory::where('game_type_id', $id)->get();

        $game_categories->each(function ($category) {
            $category->delete();
        });

        $game_type = GameType::where('id', $id)->first();

        $game_type->delete();

        return redirect()->back();

        //
    }
}
