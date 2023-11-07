<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('game_types')->insert([
            'name' => '學界賽',
            'name_secondary' => 'COMPEONATO SESCOLAR',
            'code'=>'CEJ',
            'winner_plus'=>'0',
            'language'=>'zh_TW',
            'is_language_secondary_enabled'=>1,
            'language'=>'pt'
        ]);
        DB::table('game_types')->insert([
            'name' => '公開賽',
            'code'=>'OPEN',
            'winner_plus'=>'1',
            'language'=>'zh_TW',
            'is_language_secondary_enabled'=>0,
        ]);
    }
}
