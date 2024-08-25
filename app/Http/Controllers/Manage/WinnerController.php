<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SheetWinnerService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class WinnerController extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        if($request->gender && ucfirst($request->gender)=='Men' ){
            $this->printMen();
        }
        if($request->gender && ucfirst($request->gender)=='Women' ){
            $this->printWomen();
        }
        
    }

    private function printMen(){
        $settings=null;
        $this->gameSheet=new SheetWinnerService($settings);
        //$this->gameSheet->setTitles('Main Title','sub title');
        //$this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);
        $winnerList=[
            ['title'=>'-60kg','winners'=>[
                ['place'=>'1','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'DEF','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'HIJ','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'KLM','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'OPQ','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'RST','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'UVR','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'STU','logo'=>'myloog','name'=>'Player 8'],
            ]],
            ['title'=>'-66kg','winners'=>[
                ['place'=>'1','abbr'=>'AB1','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]],
            ['title'=>'-73kg','winners'=>[
                ['place'=>'1','abbr'=>'AB2','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]]
        ];
        $this->gameSheet->pdf('Men',$winnerList);

    }
    private function printWomen(){
        $settings=null;
        $this->gameSheet=new SheetWinnerService($settings);
        //$this->gameSheet->setTitles('Main Title','sub title');
        //$this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);
        $winnerList=[
            ['title'=>'-48kg','winners'=>[
                ['place'=>'1','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'DEF','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'HIJ','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'KLM','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'OPQ','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'RST','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'UVR','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'STU','logo'=>'myloog','name'=>'Player 8'],
            ]],
            ['title'=>'-52kg','winners'=>[
                ['place'=>'1','abbr'=>'AB1','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]],
            ['title'=>'-57kg','winners'=>[
                ['place'=>'1','abbr'=>'AB2','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]]
        ];
        $this->gameSheet->pdf('Women',$winnerList);

    }


        
}
