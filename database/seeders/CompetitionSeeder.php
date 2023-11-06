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
            'competition_type_id' => '1',
            'date_start' => '2023-04-15',
            'date_end' => '2023-04-16',
            'country' => '澳門',
            'name'=>'第二十四屆學界柔道比賽賽',
            'name_secondary'=>'24th Compeonato Escolar de Judo',
            'scale'=>'本地賽',
            'days'=>'["2023-01-15","2023-04-16"]',
            'remark'=>'',
            'mat_number'=>2,
            'section_number'=>3,
            'token'=>'abc123',
            'is_cancelled'=>0,
            'language'=>'zh-TW',
            'is_language_secondary_enabled'=>true,
            'language_secondary'=>'PT',
            'status'=>0
        ]);
    }
}

