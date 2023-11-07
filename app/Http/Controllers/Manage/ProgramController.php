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

    public function gen_bouts(Program $program){
        $data=[];
        $inProgramSequence=1;
        $size=$program->chart_size+$program->chart_size/2-1;
        for($i=1;$i<=$size;$i++){
            $data[]=[
                'program_id'=>$program->id,
                'in_program_sequence'=>$inProgramSequence++,
                'sequence'=>$i,
                'queue'=>$i,
                'mat'=>$program->mat,
                'section'=>$program->section,
                'contest_system'=>$program->contest_system,
                'round'=>0,
                'turn'=>0,
                'white'=>0,
                'blue'=>0,
            ];
        };
        Bout::upsert(
            $data,
            ['program_id','in_program_sequence'],
            ['sequence','queue','mat','section','contest_system','round','turn','white','blue']
        );
        return response()->json($program);
    }
}
