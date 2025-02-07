<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionReferee;
use App\Models\Country;
use App\Models\Referee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefereeController extends Controller
{
    //
    public function index(Competition $competition)
    {
        $competition->athletes;
        return Inertia::render('Manage/Referees', [
            'competition' => $competition,
            'countries' => Country::all(),
            'programs' => $competition->programs,
            'referees' => $competition->referees()->with('referee')->get(),
        ]);
    }

    public function store(Competition $competition, Request $request)
    {
        $referee = Referee::create($request->all());

        if ($competition != null) {
            CompetitionReferee::create(['competition_id' => $competition->id, 'referee_id' => $referee->id]);
        }

        return redirect()->back();
    }

    public function update(Competition $competition, CompetitionReferee $competition_referee, Request $request)
    {
        $data = $request->all();
        if (!empty($data['serial_number'])) {
            // dd($competition);
            $competition_referee->update(['serial_number' => $data['serial_number']]);
        } else if (!empty($data['mat_number'])) {
            $competition_referee->update(['mat_number' => $data['mat_number']]);
        }

        return redirect()->back();
    }
}
