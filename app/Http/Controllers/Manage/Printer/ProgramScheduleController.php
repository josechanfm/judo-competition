<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use App\Models\Bout;
use App\Models\Competition;
use Illuminate\Http\Request;
use App\Services\Printer\ProgramScheduleService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProgramScheduleController extends Controller
{
    protected $gameSheet = null;

    public function printPdf(Request $request)
    {
        $data = $request->all();

        $bouts = Bout::whereIn('id', $data['bouts'])->orderBy('sequence')->get();
        // dd($bouts);
        $inputBouts = collect($bouts)->map(function ($bout) {
            return [
                'sequence' => $bout['sequence'],
                'category' => $bout['program']->competitionCategory->name,
                'weight' => $bout->program->convertWeight(),
                'round' => $bout->bout_name,
                'event_date' => $bout->date,
                'white_player' => $bout?->white_player->name ?? '',
                'white_team' => $bout?->white_player->team->name ?? '',
                'blue_player' => $bout?->blue_player->name ?? '',
                'blue_team' => $bout?->blue_player->team->name ?? '',
                'time' => $bout->duration_formatted,
            ];
        });
        // dd($inputBouts);

        $this->gameSheet = new ProgramScheduleService();
        $this->schedule($inputBouts, $bouts[0]->mat ?? 1);
    }

    public function schedule($bouts, $mat = 1)
    {
        if ($bouts == null) {
            $records = [
                [
                    'sequence' => 'A11',
                    'category' => 'CAT A',
                    'weight' => '56kg-',
                    'round' => 'Round 5',
                    'event_volunteerId' => 'ev001',
                    'event_title' => 'Event Title',
                    'rollno' => 'R123',
                    'event_date' => '2024-01-31',
                    'event_time' => '18:30',
                    'event_limit' => '3',
                    'time' => '04:00',
                    'white_player' => 'White player 1',
                    'white_team' => 'White team 1',
                    'blue_player' => 'Blue player 1',
                    'blue_team' => 'Blue team 1',
                ],
                [
                    'sequence' => 'A12',
                    'category' => 'CAT B',
                    'weight' => '66kg-',
                    'round' => 'Round 5',
                    'event_volunteerId' => 'ev001',
                    'event_title' => 'Event Title',
                    'rollno' => 'R123',
                    'event_date' => '2024-01-31',
                    'event_time' => '18:30',
                    'event_limit' => '3',
                    'time' => '03:00',
                    'white_player' => 'White player 2',
                    'white_team' => 'White team 2',
                    'blue_player' => 'Blue player 2',
                    'blue_team' => 'Blue team 2',
                ]

            ];
        } else {
            $records = $bouts;
        }
        // $weight="-66Kg";
        // $category="-Cadet";
        $this->gameSheet->pdf($records, 'MAT' . $mat, '2024.12.31');
    }
}
