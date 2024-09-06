<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Printer\RoundRobbinOption1Service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RoundRobbinOption1Controller extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        $filePath=storage_path('setting/game_round_robbin_option1.json');
        $settings = File::json($filePath);
        $this->gameSheet=new RoundRobbinOption1Service($settings);
        //$this->gameSheet->setTitles('Main Title','sub title');
        //$this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);
        if(empty($request->size)){
            $this->players5();
            return true;
        }

        switch($request->size){
            case 2:
                $this->players2();
                break;
            case 3:
                $this->players3();
                break;
            case 4:
                $this->players4();
                break;
            case 5:
                $this->players5();
                break;
        }
    }

    private function players2(){
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
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [1,2],
            [1,2],
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
        $this->gameSheet->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);

    }
    private function players3(){
        $players=[
            ['name_display'=>'White player 1'],
            ['name_display'=>'Blue player 2'],
            ['name_display'=>'White player 3'],
        ];
        $repechagePlayers=[];
        $winners=[
            [2,1,2,1],
            [1,2],
            [2]
        ];
        $sequences=[
            [1,2,3,4],
            [5,6],
            [7],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players,$repechagePlayers, $winners, $sequences, $winnerList);
       
    }
    private function players4(){

        $players=[
            ['name_display'=>'White player 1'],
            ['name_display'=>'Blue player 2'],
            ['name_display'=>'White player 3'],
            ['name_display'=>'Blue player 4'],
        ];
        $repechagePlayers=[
            ['name_display'=>'Player r1'],
            ['name_display'=>'Player r2']
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [2,1,2,1,1,2,1,2],
            [1,2,1,2],
            [2,1],
            [1]
        ];
        $sequences=[
            [1,3,4],
            [2],
            [5],
            [6],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $repechagePlayers, $winners, $sequences, $winnerList);
    }
    private function players5(){
        $players=[
            ['name_display'=>'Player 1'],
            ['name_display'=>'Player 2'],
            ['name_display'=>'Player 3'],
            ['name_display'=>'Player 4'],
            ['name_display'=>'Player 5']
        ];
        $repechagePlayers=[
            ['name_display'=>'Player r1'],
            ['name_display'=>'Player r2']
        ];
        $winners=[
            [1,2,1,2],
            [2],
            [1],
            [1]
        ];
        $sequences=[
            [1,3,4],
            [2],
            [5],
            [6],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $repechagePlayers, $winners, $sequences, $winnerList);
    }


        
}
