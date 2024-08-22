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
            '-60kg'=>[
                ['abbr'=>'ABC','logo'=>'myloog','Player 1'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 2'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 3'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 4'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 5'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 6'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 7'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 8'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 9'],
            ],
            '-66kg'=>[
                ['abbr'=>'ABC','logo'=>'myloog','Player 1'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 2'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 3'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 4'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 5'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 6'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 7'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 8'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 9'],
            ],
            '-72kg'=>[
                ['abbr'=>'ABC','logo'=>'myloog','Player 1'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 2'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 3'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 4'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 5'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 6'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 7'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 8'],
                ['abbr'=>'ABC','logo'=>'myloog','Player 9'],
            ]
        ];
        $this->gameSheet->pdf($winnerList);
        
    }


        
}
