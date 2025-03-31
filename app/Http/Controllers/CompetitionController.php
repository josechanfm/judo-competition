<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function show(Competition $competition, Request $request){
        if(!session()->has('token')){
            dd($request->all());
        }
        return Inertia::render('Staff/CompetitionShow',[
            'competition'=>$competition,
            'bouts'=>$competition->bouts()->get(),
        ]);
    }
    public function getQrCode(Competition $competition){
        return Inertia::render('Staff/CompetitionQrCode',[
            'competition'=>$competition
        ]);
    }
}
