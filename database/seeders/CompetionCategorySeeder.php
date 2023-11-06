<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompetionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('competition_categories')->insert([
            'abbr' => 'INTL',
            'title' => 'Inernantional',
        ]);
        DB::table('competition_categories')->insert([
            'abbr' => 'REGIN',
            'title' => 'Reginal',
        ]);
        DB::table('competition_categories')->insert([
            'abbr' => 'LOCAL',
            'title' => 'Local',
        ]);
        DB::table('competition_categories')->insert([
            'abbr' => 'SCORE',
            'title' => 'Scoreing',
        ]);
    }
}
