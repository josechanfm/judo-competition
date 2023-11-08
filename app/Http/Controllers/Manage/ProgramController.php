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
        $sections=
        $bouts=$competition->bouts;
        $sections=$bouts->groupBy('section');
        $sectionMats=[];
        foreach($sections as $id=>$section){
            $sectionMats[$id]=$section->groupBy('mat');
        };
        dd($sectionMats);
        $i=1;
        foreach($bouts as $bout){
            $bout->sequence=$i++;
            $bout->save();
            echo json_encode($bout);
            echo '<br>';
        }
        dd($bouts);
        $programs=$competition->programs->sortBy('sequence')->sortByDesc('chart_size');
        // foreach($programs as $program){
        //     echo json_encode($program);
        //     echo '<br>';
        // }
        // dd($programs->pluck('chart_size'));
        $data=[];
        $sequence=1;
        foreach($programs as $program){
            $round=$program->chart_size;
            $inProgramSequence=1;
            while($round > 0){
                $roundSize=$round/2;
                for($t=1;$t<=$roundSize;$t++){
                    $data[]=[
                        'program_id'=>$program->id,
                        'sequence'=>$sequence++,
                        'in_program_sequence'=>$inProgramSequence++,
                        'queue'=>0,
                        'mat'=>$program->mat,
                        'section'=>$program->section,
                        'contest_system'=>$program->contest_system,
                        'round'=>$round,
                        'turn'=>0,
                        'white'=>0,
                        'blue'=>0,
                    ];
                }
                //repeatcharge round 1
                if($roundSize==4){
                    $data[]=[
                        'program_id'=>$program->id,
                        'sequence'=>$sequence++,
                        'in_program_sequence'=>$inProgramSequence++,
                        'queue'=>0,
                        'mat'=>$program->mat,
                        'section'=>$program->section,
                        'contest_system'=>$program->contest_system,
                        'round'=>7,
                        'turn'=>0,
                        'white'=>0,
                        'blue'=>0,
                    ];
                    $data[]=[
                        'program_id'=>$program->id,
                        'sequence'=>$sequence++,
                        'in_program_sequence'=>$inProgramSequence++,
                        'queue'=>0,
                        'mat'=>$program->mat,
                        'section'=>$program->section,
                        'contest_system'=>$program->contest_system,
                        'round'=>7,
                        'turn'=>0,
                        'white'=>0,
                        'blue'=>0,
                    ];
                }
                if($roundSize==2 && $program->chart_size>4){
                    $data[]=[
                        'program_id'=>$program->id,
                        'sequence'=>$sequence++,
                        'in_program_sequence'=>$inProgramSequence++,
                        'queue'=>0,
                        'mat'=>$program->mat,
                        'section'=>$program->section,
                        'contest_system'=>$program->contest_system,
                        'round'=>3,
                        'turn'=>0,
                        'white'=>0,
                        'blue'=>0,
                    ];
                    $data[]=[
                        'program_id'=>$program->id,
                        'sequence'=>$sequence++,
                        'in_program_sequence'=>$inProgramSequence++,
                        'queue'=>0,
                        'mat'=>$program->mat,
                        'section'=>$program->section,
                        'contest_system'=>$program->contest_system,
                        'round'=>3,
                        'turn'=>0,
                        'white'=>0,
                        'blue'=>0,
                    ];

                }
                $round=$round/2;
            }

            // if($program->contest_system=='erm'){
            //     for($j=1;$j<=4;$j++){
            //         $data[]=[
            //             'program_id'=>$program->id,
            //             'in_program_sequence'=>$inProgramSequence++,
            //             'sequence'=>$sequence++,
            //             'queue'=>0,
            //             'mat'=>$program->mat,
            //             'section'=>$program->section,
            //             'contest_system'=>$program->contest_system,
            //             'round'=>$r,
            //             'turn'=>0,
            //             'white'=>0,
            //             'blue'=>0,
            //         ];
            //     }
            // }

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
