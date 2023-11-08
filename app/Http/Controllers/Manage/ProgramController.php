<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Competition;
use App\Models\Program;
use App\Models\Bout;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Competition $competition)
    {
        $competition->programs;
        //$competition->categories;
        return Inertia::render('Manage/Programs',[
            'competition'=>$competition
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition, Program $program)
    {
        $program->bouts;
        $program->athletes;
        return Inertia::render('Manage/Program',[
            'program'=>$program
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function gen_bouts(Competition $competition){
        $programs=$competition->programs->sortBy('sequence');
        $data=[];
        $inProgramSequence=1;
        foreach($programs as $program){
            $round=(int)log($program->chart_size,2);
            $roundSize=$program->chart_size;
            $sequence=1;
            for($r=1;$r<=$round;$r++){
                $roundSize=$roundSize/2;
                for($t=1;$t<=$roundSize;$t++){
                    $data[]=[
                        'program_id'=>$program->id,
                        'in_program_sequence'=>$inProgramSequence++,
                        'sequence'=>$sequence++,
                        'queue'=>0,
                        'mat'=>$program->mat,
                        'section'=>$program->section,
                        'contest_system'=>$program->contest_system,
                        'round'=>$r,
                        'turn'=>0,
                        'white'=>0,
                        'blue'=>0,
                    ];
                }
            }
        }
        //dd($data);
        Bout::upsert(
            $data,
            ['program_id','in_program_sequence'],
            ['sequence','queue','mat','section','contest_system','round','turn','white','blue']
        );
        //$programs=$competition->programs->sortByDesc('chart_size');

        return response()->json($program);
    }
}
