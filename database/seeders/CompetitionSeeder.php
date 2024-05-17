<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('competitions')->insert([
            'date_start' => '2024-04-15',
            'date_end' => '2024-04-16',
            'country' => '澳門',
            'name' => '第二十四屆學界柔道比賽',
            'name_secondary' => '24th Compeonato Escolar de Judo',
            'scale' => '本地賽',
            'days' => '["2024-04-15","2024-04-16"]',
            'remark' => '',
            'mat_number' => 2,
            'section_number' => 3,
            'token' => 'abc123',
            'is_cancelled' => 0,
            'status' => 0
        ]);
        DB::table('competition_categories')->insert([
            'competition_id' => 1,
            'name' => 'A組',
            'name_secondary' => 'Untitled',
            'code' => 'A',
            'weights' => '["MW55-","MW60-","MW65-","MW72-","MW78-","FW48-"]',
            'duration' => 240
        ]);
        DB::table('competition_type')->insert([
            'competition_id' => 1,
            'name' => '學界賽',
            'name_secondary' => 'COMPEONATO SESCOLAR',
            'code' => 'CEJ',
            'winner_plus' => '0',
            'language' => 'zh_TW',
            'is_language_secondary_enabled' => 1,
            'language' => 'pt'
        ]);
    }
}
