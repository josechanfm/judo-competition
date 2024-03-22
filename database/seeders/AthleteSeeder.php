<?php

namespace Database\Seeders;

use App\Models\Athlete;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Program;

class AthleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Athlete::factory(1)->count(200)->create(['competition_id' => 1, 'team_id' => 1]);

        $a = 1;
        for ($p = 1; $p <= 5; $p++) {
            $program = Program::find($p);
            for ($i = 1; $i <= $program->chart_size; $i++) {
                DB::table('athlete_program')->insert([
                    'program_id' => $program->id,
                    'athlete_id' => $a++,
                ]);
            };
        }
    }
}
