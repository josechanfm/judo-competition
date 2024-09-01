<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProgramScheduleService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProgramScheduleController extends Controller
{
    protected $gameSheet=null;

    public function printPdf(Request $request){
        $this->gameSheet=new ProgramScheduleService();
        $this->schedule();
    }

    public function schedule(){
        $records=[
            [
                'sequence'=>'A11',
                'category'=>'CAT A',
                'weight'=>'56kg-',
                'round'=>'Round 5',
                'event_volunteerId'=>'ev001',
                'event_title'=>'Event Title',
                'rollno'=>'R123',
                'event_date'=>'2024-01-31',
                'event_time'=>'18:30',
                'event_limit'=>'3',
                'time'=>'04:00',
                'white_player'=>'White player 1',
                'white_team'=>'White team 1',
                'blue_player'=>'Blue player 1',
                'blue_team'=>'Blue team 1',
            ],[
                'sequence'=>'A12',
                'category'=>'CAT B',
                'weight'=>'66kg-',
                'round'=>'Round 5',
                'event_volunteerId'=>'ev001',
                'event_title'=>'Event Title',
                'rollno'=>'R123',
                'event_date'=>'2024-01-31',
                'event_time'=>'18:30',
                'event_limit'=>'3',
                'time'=>'03:00',
                'white_player'=>'White player 2',
                'white_team'=>'White team 2',
                'blue_player'=>'Blue player 2',
                'blue_team'=>'Blue team 2',
            ]

        ];
        $this->gameSheet->pdf($records);

    }

}
