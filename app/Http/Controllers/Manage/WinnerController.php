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
        $settings=null;
        $this->gameSheet=new SheetWinnerService($settings);
        //$this->gameSheet->setTitles('Main Title','sub title');
        //$this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        //$this->gameSheet->setRepechage(null);
        $winnerList=[
            ['title'=>'-60kg','winners'=>[
                ['place'=>'1','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]],
            ['title'=>'-66kg','winners'=>[
                ['place'=>'1','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]],
            ['title'=>'-72kg','winners'=>[
                ['place'=>'1','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 1'],
                ['place'=>'2','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 2'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 3'],
                ['place'=>'3','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 4'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 5'],
                ['place'=>'5','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 6'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 7'],
                ['place'=>'7','abbr'=>'ABC','logo'=>'myloog','name'=>'Player 8'],
            ]]
        ];
        $this->gameSheet->pdf($winnerList);
        
    }


        
}
