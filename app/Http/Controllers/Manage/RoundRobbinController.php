<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SheetRoundRobbinService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RoundRobbinController extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        $settings=null;
        $this->gameSheet=new SheetRoundRobbinService($settings);
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
            case 6:
                $this->players6();
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
    private function players6(){
        $players=[
            [
                'white'=>['name_display'=>'White player 1'],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ],[
                'white'=>['name_display'=>'White player 5'],
                'blue'=>['name_display'=>'Blue player 6'],
            ],[
                'white'=>['name_display'=>'White player 7'],
                'blue'=>['name_display'=>'Blue player 8'],
            ],[
                'white'=>['name_display'=>'White player 9'],
                'blue'=>['name_display'=>'Blue player 10'],
            ],[
                'white'=>['name_display'=>'White player 11'],
                'blue'=>['name_display'=>'Blue player 12'],
            ],[
                'white'=>['name_display'=>'White player 13'],
                'blue'=>['name_display'=>'Blue player 14'],
            ],[
                'white'=>['name_display'=>'White player 15'],
                'blue'=>['name_display'=>'Blue player 16'],
            ],[
                'white'=>['name_display'=>'White player 17'],
                'blue'=>['name_display'=>'Blue player 18'],
            ],[
                'white'=>['name_display'=>'White player 19'],
                'blue'=>['name_display'=>'Blue player 20'],
            ],[
                'white'=>['name_display'=>'White player 21'],
                'blue'=>['name_display'=>'Blue player 22'],
            ],[
                'white'=>['name_display'=>'White player 23'],
                'blue'=>['name_display'=>'Blue player 24'],
            ],[
                'white'=>['name_display'=>'White player 25'],
                'blue'=>['name_display'=>'Blue player 26'],
            ],[
                'white'=>['name_display'=>'White player 27'],
                'blue'=>['name_display'=>'Blue player 28'],
            ],[
                'white'=>['name_display'=>'White player 29'],
                'blue'=>['name_display'=>'Blue player 30'],
            ],[
                'white'=>['name_display'=>'White player 31'],
                'blue'=>['name_display'=>'Blue player 32'],
            ],[
                'white'=>['name_display'=>'White player 33'],
                'blue'=>['name_display'=>'Blue player 34'],
            ],[
                'white'=>['name_display'=>'White player 35'],
                'blue'=>['name_display'=>'Blue player 36'],
            ],[
                'white'=>['name_display'=>'White player 37'],
                'blue'=>['name_display'=>'Blue player 38'],
            ],[
                'white'=>['name_display'=>'White player 39'],
                'blue'=>['name_display'=>'Blue player 40'],
            ],[
                'white'=>['name_display'=>'White player 41'],
                'blue'=>['name_display'=>'Blue player 42'],
            ],[
                'white'=>['name_display'=>'White player 43'],
                'blue'=>['name_display'=>'Blue player 44'],
            ],[
                'white'=>['name_display'=>'White player 45'],
                'blue'=>['name_display'=>'Blue player 46'],
            ],[
                'white'=>['name_display'=>'White player 47'],
                'blue'=>['name_display'=>'Blue player 48'],
            ],[
                'white'=>['name_display'=>'White player 49'],
                'blue'=>['name_display'=>'Blue player 50'],
            ],[
                'white'=>['name_display'=>'White player 51'],
                'blue'=>['name_display'=>'Blue player 52'],
            ],[
                'white'=>['name_display'=>'White player 53'],
                'blue'=>['name_display'=>'Blue player 54'],
            ],[
                'white'=>['name_display'=>'White player 55'],
                'blue'=>['name_display'=>'Blue player 56'],
            ],[
                'white'=>['name_display'=>'White player 57'],
                'blue'=>['name_display'=>'Blue player 58'],
            ],[
                'white'=>['name_display'=>'White player 59'],
                'blue'=>['name_display'=>'Blue player 60'],
            ],[
                'white'=>['name_display'=>'White player 61'],
                'blue'=>['name_display'=>'Blue player 62'],
            ],[
                'white'=>['name_display'=>'White player 31'],
                'blue'=>['name_display'=>'Blue player 32'],
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [2,1,2,1,1,2,1,2],
            [1,2,1,2],
            [2,1],
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
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,24,26,27,28,29,30,31,32],
            [33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48],
            [49,50,51,52,53,54,55,56],
            [57,58,59,60],
            [61,62],
            [63],
            [64,65],
            [67,66]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);
        
    }

        
}
