<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SheetRoundRobbinOption1Service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RoundRobbinOption1Controller extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        $settings=null;
        $this->gameSheet=new SheetRoundRobbinOption1Service($settings);
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
            case 3:
                $this->players3();
            case 4:
                $this->players4();
            case 5:
                $this->players5();
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
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
       
    }
    private function players4(){
        $players=[
            ['name_display'=>'White player 1'],
            ['name_display'=>'Blue player 2'],
            ['name_display'=>'White player 3'],
            ['name_display'=>'Blue player 4'],
        ];
        $winners=[
            [1,2,1,2,1,2,1,2],
            [2,1,2,1],
            [1,2],
            [2]
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8],
            [9,10,11,12],
            [13,14],
            [15],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
    }
    private function players5(){
        $players=[
            ['name_display'=>'White player 1'],
            ['name_display'=>'Blue player 2'],
            ['name_display'=>'White player 3'],
            ['name_display'=>'Blue player 4'],
            ['name_display'=>'White player 5']
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [2,1,2,1,1,2,1,2],
            [1,2,1,2],
            [2,1],
            [1]
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
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
            [17,18,19,20,21,22,23,24],
            [24,26,27,28],
            [29,30],
            [31],
            [32,33],
            [34,35]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
    }


        
}
