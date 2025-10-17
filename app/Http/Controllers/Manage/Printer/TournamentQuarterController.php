<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Printer\TournamentQuarterService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Program;

class TournamentQuarterController extends Controller
{
    protected $gameSheet = null;
    protected $dummyData = null;

    public function printPdf(Request $request)
    {
        // dd($request);
        $program =  Program::where('id', $request->program)->first();
        $size = $program->chart_size ?? $request->size;
        // dd($program->bouts[0]->white_player->name_display);
        $players = $program?->bouts->map(function ($bout, $index) use ($size) {
            if ($index > ($size / 2 - 1)) {
                return;
            } else {
                return [
                    'white' => ['name_display' => $bout->white_player->name_display],
                    'blue' => ['name_display' => $bout->blue_player->name_display]
                ];
            }
        })->reject(function ($bout) {
            return empty($bout);
        }) ?? null;
        $winners = null;
        // dd($players);
        $round3Bouts = $program?->bouts->filter(function ($bout) {
            return $bout->round == 3;
        });
        $round2Bouts = $program?->bouts->filter(function ($bout) {
            return $bout->round == 2;
        });
        if ($round3Bouts != null) {
            $repechagePlayers = [
                $round3Bouts->map(function ($bout) {
                    return [
                        'white' => ['name_display' => $bout->white_player->name_display ?? '', 'from' => $bout->whiteRiseFrom($bout)->in_program_sequence],
                        'blue' => ['name_display' => $bout->blue_player->name_display ?? '', 'from' => $bout->blueRiseFrom($bout)->in_program_sequence],
                    ];
                })->values()->all(),
                $round2Bouts->map(function ($bout) {
                    return [
                        'blue' => ['name_display' => $bout->white_player->name_display ?? '', 'from' => $bout->whiteRiseFrom($bout)->in_program_sequence],
                        'blue' => ['name_display' => $bout->blue_player->name_display ?? '', 'from' => $bout->blueRiseFrom($bout)->in_program_sequence],
                    ];
                })->values()->all()
            ];
        } else {
            $repechagePlayers = null;
        }
        $filePath = storage_path('setting/game_tournament_quarter.json');
        $settings = File::json($filePath);

        $dummyPath = storage_path('setting/game_tournament_quarter_dataset.json');
        $this->dummyData = File::json($dummyPath);

        //};
        $this->gameSheet = new TournamentQuarterService($settings);
        $this->gameSheet->setTitles('Main Title', 'sub title');
        $this->gameSheet->setFonts('times', 'cid0ct', 'times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);

        $poolLable = [
            ['name' => '分組 A'],
            ['name' => '分組 B'],
            ['name' => '分組 C'],
            ['name' => '分組 D']
        ];
        $this->gameSheet->setLogos('images/jua_logo.png', 'images/mja_logo.png');

        $this->gameSheet->setPoolLabel($poolLable);
        // dd($request->winner_line);
        if ($request->winner_line) {
            $this->gameSheet->setWinnerLine(true);
        }
        // dd($players);
        if (empty($size)) {
            $this->players32();
            return true;
        }
        switch ($size) {
            case 4:
                $this->players4($players, $winners, $repechagePlayers);
                break;
            case 8:
                $this->players8($players, $winners, $repechagePlayers);
                break;
            case 16:
                $this->players16($players, $winners, $repechagePlayers);
                break;
            case 32:
                $this->players32($players, $winners, $repechagePlayers);
                break;
            case 64:
                $this->players64($players, $winners, $repechagePlayers);
                break;
        }
    }

    private function players4($players = null, $winners = null)
    {
        if ($players == null) {
            foreach ($this->dummyData[4] as $key => $value) {
                $$key = $value;
            }
        }
        $this->gameSheet->pdf($players, $winners,  $sequences, $winnerList);
    }
    
    private function players8($players = null, $winners = null, $repechagePlayers = null)
    {
        if ($players == null) {
            foreach ($this->dummyData[8] as $key => $value) {
                $$key = $value;
            }
        }
        $sequences = [
            [1, 2, 3, 4],
            [5, 6],
            [11],
            [7, 8],
            [9, 10]
        ];

        // $winners = [[1, 1, 1, 1], [1, 1], [1], [1, 1], [1, 1]];
        $winnerList = [
            ['award' => 'Gold', 'name' => ''],
            ['award' => 'Silver', 'name' => ''],
            ['award' => 'Brown', 'name' => ''],
            ['award' => 'Brown', 'name' => ''],
        ];
        $this->gameSheet->pdf($players, $winners,  $sequences, $winnerList, $repechagePlayers);
    }
    private function players16($players = null, $winners = null, $repechagePlayers = null)
    {
        if ($players == null) {
            foreach ($this->dummyData[16] as $key => $value) {
                $$key = $value;
            }
        }
        $this->gameSheet->setFonts('times', 'cid0kr', 'times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
    }
    private function players32($players = null, $winners = null, $repechagePlayers = null)
    {
        if ($players == null) {
            foreach ($this->dummyData[32] as $key => $value) {
                $$key = $value;
            }
        }
        $this->gameSheet->setFonts('times', 'times', 'times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
    }
    private function players64($players = null, $winners = null, $repechagePlayers = null)
    {
        if ($players == null) {
            foreach ($this->dummyData[64] as $key => $value) {
                $$key = $value;
            }
        }
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
    }
}
