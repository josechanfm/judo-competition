<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Printer\TournamentDoubleService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TournamentDoubleController extends Controller
{
    protected $gameSheet=null;
    protected $dummyData = null;

    public function printPdf(Request $request){
        $filePath=storage_path('setting/game_tournament_double.json');
        $settings = File::json($filePath);
        $dummyPath = storage_path('setting/game_tournament_double_dataset.json');
        $this->dummyData = File::json($dummyPath);

        $this->gameSheet=new TournamentDoubleService($settings);
        $this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        $poolLable=[
            ['name'=>'分組 A'],
            ['name'=>'分組 B'],
            ['name'=>'分組 C'],
            ['name'=>'分組 D']
        ];
        $this->gameSheet->setPoolLabel($poolLable);
        if($request->winner_line){
            $this->gameSheet->setWinnerLine(true);
        }

        if(empty($request->size)){
            $this->players32();
            return true;
        }
        switch($request->size){
            case 4:
                //$this->players4();
            case 8:
                //$this->players8();
            case 16:
                $this->players16();
                break;
            case 32:
                $this->players32();
                break;
            case 64:
                $this->players64();
                break;
        }
    }

    private function players4(){
        $players=[
            [
                'white'=>['name_display'=>'White player 1','status'=>false],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ]
        ];
        $winners=[
            [1,2],
            [2]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1','from'=>'A1'],
                'blue'=>['name_display'=>'Blue player r2','from'=>'A1'],
            ],[
                'blue'=>['name_display'=>'Blue player r5','from'=>'A1'],
            ],[
                'white'=>['name_display'=>'White player r3','from'=>'A1'],
                'blue'=>['name_display'=>'Blue player r4','from'=>'A1'],
            ],[
                'blue'=>['name_display'=>'Blue player r6','from'=>'A1'],
            ]
        ];
        $sequences=[
            [1,2],
            [3],
            [4,5],
            [6,7],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);

    }
    private function players8(){
        $players=[
            [
                'white'=>['name_display'=>'運動員 1'],
                'blue'=>['name_display'=>'josé joão 2'],
            ],[
                'white'=>['name_display'=>'애정) 3'],
                'blue'=>['name_display'=>'简体中文 4'],
            ],[
                'white'=>['name_display'=>'日本人の氏名'],
                'blue'=>['name_display'=>'Blue player 6'],
            ],[
                'white'=>['name_display'=>'White player 7'],
                'blue'=>['name_display'=>'Blue player 8'],
            ]
        ];
        $winners=[
            [2,1,2,1],
            [1,2],
            [2]
        ];
        $repechagePlayers=[
            [
                [
                    'white'=>['name_display'=>'White player (A1)r1','from'=>'A1'],
                    'blue'=>['name_display'=>'Blue player (A2)r2','from'=>'A2'],
                ],[
                    'white'=>['name_display'=>'White player (B1)r1','from'=>'B1'],
                    'blue'=>['name_display'=>'Blue player (B2)r5','from'=>'B2'],
                ],[
                    'white'=>['name_display'=>'White player (C1)r1','from'=>'C1'],
                    'blue'=>['name_display'=>'Blue player (C2)r5','from'=>'C2'],
                ],[
                    'white'=>['name_display'=>'White player (D1)r1','from'=>'D1'],
                    'blue'=>['name_display'=>'Blue player (D2)r5','from'=>'D2'],
                ]
            ],
            [null,null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'18']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'19']],
            ]
        ];
        $sequences=[
            [1,2,3,4],
            [5,6],
            [7],
            [8,9,10,11],
            [10,11],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
       
    }
    private function players16(){
        foreach($this->dummyData[16] as $key=>$value){
            $$key=$value;
        }

        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
    }
    private function players32(){
        foreach($this->dummyData[32] as $key=>$value){
            $$key=$value;
        }
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
    }
    private function players64(){
        foreach($this->dummyData[64] as $key=>$value){
            $$key=$value;
        }
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechagePlayers);
        
    }

    
}
