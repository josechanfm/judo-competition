<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Printer\RefereeService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RefereeController extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        $this->gameSheet=new RefereeService();
        $this->schedule();
    }

    public function schedule(){
        $records=[
            [
                'sequence'=>'1',
                'logo'=>"images/flags/tn_af-flag.gif",
                'nation'=>'MAC',
                'family_name'=>'FAMILY NAME 1',
                'given_name'=>'Given Name 1',
                'number'=>'1',
                'mat'=>'I'
            ],[
                'sequence'=>'2',
                'logo'=>"images/flags/tn_af-flag.gif",
                'nation'=>'HKG',
                'category'=>'Cadet',
                'family_name'=>'FAMILY NAME 2',
                'given_name'=>'Given Name 2',
                'number'=>'2',
                'mat'=>'II'
            ]

        ];
        $this->gameSheet->setTitles('Referee List','Judo Union of Asia');
        $this->gameSheet->pdf($records,'REF','2020.12.31');

    }

}
