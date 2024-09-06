<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Printer\WeightInService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class WeightInController extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        $this->gameSheet=new WeightInService();
        $this->schedule();
    }

    public function schedule(){
        $records=[
            [
                'sequence'=>'1',
                'logo'=>"images/flags/tn_af-flag.gif",
                'team'=>'MAC',
                'family_name'=>'FAMILY NAME 1',
                'given_name'=>'Given Name 1',
                'dob'=>'18-07-2000',
            ],[
                'sequence'=>'2',
                'logo'=>"images/flags/tn_af-flag.gif",
                'team'=>'MAC',
                'category'=>'Cadet',
                'family_name'=>'FAMILY NAME 2',
                'given_name'=>'Given Name 2',
                'dob'=>'18-07-2000',
            ]

        ];
        $this->gameSheet->setTitles('Weight-in List','Judo Union of Asia');
        $this->gameSheet->pdf($records,'-66','Cadet');

    }

}
