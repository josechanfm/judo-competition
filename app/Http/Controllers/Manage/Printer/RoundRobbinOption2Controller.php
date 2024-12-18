<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Services\Printer\RoundRobbinOption2Service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RoundRobbinOption2Controller extends Controller
{
    protected $gameSheet = null;
    protected $dummyData = null;

    public function printPdf(Request $request)
    {
        $filePath = storage_path('setting/game_round_robbin_option2.json');
        $settings = File::json($filePath);
        $dummyPath = storage_path('setting/game_tournament_quarter_dataset.json');
        $this->dummyData = File::json($dummyPath);

        $this->gameSheet = new RoundRobbinOption2Service($settings);
        //$this->gameSheet->setTitles('Main Title','sub title');
        $this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);
        $program =  Program::where('id', $request->program)->first();
        // dd($program->athletes);
        $size = $program->chart_size ?? $request->size;
        // dd($program->bouts[0]->white_player->name_display);
        $players = $program?->athletes->map(function ($athlete, $index) use ($size) {
            if ($index > ($size - 1)) {
                return;
            } else {
                return ['name_display' => $athlete->name_display];
            }
        })->reject(function ($athlete) {
            return empty($athlete);
        }) ?? null;
        if (empty($request->size)) {
            $this->players5($players);
            return true;
        }
        
        switch ($request->size) {
            case 2:
                $this->players2($players);
            case 3:
                $this->players3($players);
            case 4:
                $this->players4($players);
            case 5:
                $this->players5($players);
        }
    }

    private function players2()
    {
        $players = [
            [
                'white' => ['name_display' => 'White player 1', 'status' => false],
                'blue' => ['name_display' => 'Blue player 2'],
            ],
            [
                'white' => ['name_display' => 'White player 3'],
                'blue' => ['name_display' => 'Blue player 4'],
            ]
        ];
        $winners = [
            [1, 2],
            [2]
        ];
        $repechagePlayers = [
            [
                'white' => ['name_display' => 'White player r1'],
                'blue' => ['name_display' => 'Blue player r2'],
            ],
            [
                'blue' => ['name_display' => 'Blue player r5'],
            ],
            [
                'white' => ['name_display' => 'White player r3'],
                'blue' => ['name_display' => 'Blue player r4'],
            ],
            [
                'blue' => ['name_display' => 'Blue player r6'],
            ]
        ];
        $repechageWinners = [
            [1, 2],
            [1, 2],
        ];
        $sequences = [
            [1, 2],
            [3],
            [4, 5],
            [6, 7],
        ];
        $winnerList = [
            ['award' => 'Gold', 'name' => 'Place 1'],
            ['award' => 'Silver', 'name' => 'Place 2'],
            ['award' => 'Brown', 'name' => 'Place 3'],
            ['award' => 'Brown', 'name' => 'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);
    }
    private function players3($players)
    {
        $winners = null;
        $sequences = [
            [1, 2, 3, 4],
            [5, 6],
            [7],
        ];
        $winnerList = [
            ['award' => 'Gold', 'name' => 'Place 1'],
            ['award' => 'Silver', 'name' => 'Place 2'],
            ['award' => 'Brown', 'name' => 'Place 3'],
            ['award' => 'Brown', 'name' => 'Place 4'],
        ];
        $this->gameSheet->setFonts('times', 'cid0ct', 'times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
    }
    private function players4($players)
    {
        $winners = null;
        $sequences = [
            [1, 2, 3, 4, 5, 6, 7, 8],
            [9, 10, 11, 12],
            [13, 14],
            [15],
        ];
        $winnerList = [
            ['award' => 'Gold', 'name' => 'Place 1'],
            ['award' => 'Silver', 'name' => 'Place 2'],
            ['award' => 'Brown', 'name' => 'Place 3'],
            ['award' => 'Brown', 'name' => 'Place 4'],
        ];
        
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
    }
    private function players5($players)
    {
        if ($players == null) {
            foreach ($this->dummyData[5] as $key => $value) {
                $$key = $value;
            }
        }
        // dd($players);
        $sequences = [
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
            [17, 18, 19, 20, 21, 22, 23, 24],
            [24, 26, 27, 28],
            [29, 30],
            [31],
            [32, 33],
            [34, 35]
        ];
        $winners = null;
        $winnerList = [
            ['award' => 'Gold', 'name' => 'Place 1'],
            ['award' => 'Silver', 'name' => 'Place 2'],
            ['award' => 'Brown', 'name' => 'Place 3'],
            ['award' => 'Brown', 'name' => 'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
    }
}
