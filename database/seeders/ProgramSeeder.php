<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->insert([
            'competition_id' => 1,
            'competition_category_id' => 1,
            'sequence' => 1,
            'date' => '2023-04-15',
            'weight_code' => 'MW55-',
            'mat' => '1',
            'section' => '1',
            'contest_system' => 'erm',
            'chart_size' => '4',
            'duration' => 240,
            'status' => 0
        ]);
        DB::table('programs')->insert([
            'competition_id' => 1,
            'competition_category_id' => 1,
            'sequence' => 2,
            'date' => '2023-04-15',
            'weight_code' => 'MW60-',
            'mat' => '1',
            'section' => '1',
            'contest_system' => 'erm',
            'chart_size' => '8',
            'duration' => 240,
            'status' => 0
        ]);
        DB::table('programs')->insert([
            'competition_id' => 1,
            'competition_category_id' => 1,
            'sequence' => 3,
            'weight_code' => 'MW65-',
            'date' => '2023-04-15',
            'mat' => '1',
            'section' => '1',
            'contest_system' => 'erm',
            'chart_size' => '16',
            'duration' => 240,
            'status' => 0
        ]);
        DB::table('programs')->insert([
            'competition_id' => 1,
            'competition_category_id' => 1,
            'sequence' => 4,
            'date' => '2023-04-15',
            'weight_code' => 'MW72-',
            'mat' => '1',
            'section' => '1',
            'contest_system' => 'erm',
            'chart_size' => '32',
            'duration' => 240,
            'status' => 0
        ]);
        DB::table('programs')->insert([
            'competition_id' => 1,
            'competition_category_id' => 1,
            'sequence' => 5,
            'date' => '2023-04-15',
            'weight_code' => 'MW78-',
            'mat' => '1',
            'section' => '1',
            'contest_system' => 'erm',
            'chart_size' => '64',
            'duration' => 240,
            'status' => 0
        ]);
    }
}
