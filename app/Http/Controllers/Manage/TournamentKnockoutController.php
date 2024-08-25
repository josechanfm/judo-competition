<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SheetTournamentQuarterService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TournamentKnockoutController extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        // $data = json_decode(Storage::get('setting/game_sheet.json'), true);
        // dd($data);
        // dd(storage_path('setting/game_sheet.json'));
        $filePath=storage_path('setting/game_tournament_knockout.json');
        // if(File::exists($filePath)){
            $settings = File::json($filePath);
        //};
        $this->gameSheet=new SheetTournamentQuarterService($settings);
        //$this->gameSheet->setTitles('Main Title','sub title');
        $this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);
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
                $this->players4();
            case 8:
                $this->players8();
            case 16:
                $this->players16();
            case 32:
                $this->players32();
            case 64:
                $this->players64();
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
        $sequences=[
            [1,2],
            [3],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);

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
    private function players16(){
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
            ]
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
    private function players32(){
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
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [2,1,2,1,1,2,1,2],
            [1,2,1,2],
            [2,1],
            [1]
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
            [17,18,19,20,21,22,23,24],
            [24,26,27,28],
            [29,30],
            [31],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList);
    }
    private function players64(){
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
        $sequences=[
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,24,26,27,28,29,30,31,32],
            [33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48],
            [49,50,51,52,53,54,55,56],
            [57,58,59,60],
            [61,62],
            [63],
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
