<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    //
    public function getCompetition($token) {
        $competition = Competition::where('token', $token)->first();

        return response()->json([
            'competition' => $competition,
        ]);
    }

    public function getCompetitionBouts($token, Request $request)
    {
        $competition = Competition::where('token', $token)->first();

        if (!$competition) {
            return response()->json([
                'error' => 'Competition not found'
            ], 404);
        }

        $bouts = $competition->bouts()
            ->where('mat', $request->mat)
            ->where('section', $request->section)
            ->where('queue', '!=', 0)
            ->where('status', 0)
            ->orderBy('queue')
            ->get();

        // 重新整理數據
        $formattedBouts = $bouts->map(function ($bout) {
            return [
                'queue' => $bout->queue,
                'white_player' => [
                    'name_display' => ($bout->white_player->name ?? null) . ($bout->white_player->name_secondary ?? null),
                    'team' => $bout->white_player->team->name ?? null,
                ],
                'blue_player' => [
                    'name_display' => ($bout->blue_player->name ?? null) . ($bout->blue_player->name_secondary ?? null),
                    'team' => $bout->blue_player->team->name ?? null,
                ],
                'category' => $bout->program->competitionCategory->name ?? null,
                'weight' => $bout->program ? $bout->program->convertWeight() : null,
                'program_id' => $bout->program_id,
            ];
        });

        return response()->json([
            'bouts' => $formattedBouts,
        ]);
    }
    public function sendCompetition(Competition $competition) {}
}
