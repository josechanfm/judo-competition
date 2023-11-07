<?php

namespace Database\Seeders;

use App\Models\Athlete;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AthleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Athlete::factory(1)->count(50)->create(['competition_id'=>1]);

        for($i=1;$i<=10;$i++){
            DB::table('athlete_program')->insert([
                'program_id' => '1',
                'athlete_id' => $i,
            ]);
        };
   
    }
}
