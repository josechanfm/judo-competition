<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\Bout;

class BoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competition=Competition::find(1);
        $programAthletes=$competition->programsAthletes;
        $seq=0;
        foreach($programAthletes as $program){
            $programSeq=0;
            for($i=0;$i<$program->chart_size;$i+2){
                Bout::create([
                    'program_id'=>$program->id,
                    'sequence'=>$seq++,
                    'in_program_sequence'=>$programSeq++,
                    'white'=>$program->athlets[$i],
                    //'blue'=>$program->athlets[$i+1],
                ]);
                
            }
            
        }
        dd($competition);
    }
}
