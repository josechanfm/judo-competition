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
    }
}
