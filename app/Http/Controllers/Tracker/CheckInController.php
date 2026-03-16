<?php

namespace App\Http\Controllers\Tracker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competition;
use Inertia\Inertia;

class CheckInController extends Controller
{
    public function index(){
        $competition=Competition::find(5);
        $competition->programAthletes = $competition->programAthletes()->get();
        $competition->programsBouts;
        $date = $competition->days[0];
        $section = 1;
        $competition->bouts = $competition->bouts()
            ->where('queue', '!=', 0)
            ->orderBy('queue')
            ->with(['result'])
            ->get()
            ->map(function ($bout) {
                // 將 convertWeight 函數加入每個 bout 物件
                $bout->convertWeight = function () use ($bout) {
                    return $bout->program->convertWeight();
                };
                
                return $bout;
            });
        // dd($competition->bouts[0]->convertWeight);
        return Inertia::render('Tracker/CheckIn', [
            'competition' => $competition,
            'date' => $date,
            'section' => $section
        ]);
    }
}
