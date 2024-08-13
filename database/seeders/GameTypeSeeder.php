<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GameType;

class GameTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            [
                'name' => '學界賽',
                'name_secondary' => 'COMPEONATO SESCOLAR',
                'code'=>'CEJ',
                'winner_plus'=>'0',
                'language'=>'zh_TW',
                'is_language_secondary_enabled'=>1,
                'language'=>'pt',
                'game_categories'=>[
                    [
                        'name' => 'A組',
                        'name_secondary' => 'Untitled',
                        'code' => 'A',
                        'weights' => '["MW55-","MW60-","FW48-"]',
                        'duration' => 240
                    ]
                ]
            ],[
                'name' => '公開賽',
                'code'=>'OPEN',
                'winner_plus'=>'1',
                'language'=>'zh_TW',
                'is_language_secondary_enabled'=>0,
                'game_categories'=>[]
            ]

        ];
        foreach($data as $d){
            $gameCategories=$d['game_categories'];
            unset($d['game_categories']);
            $gameType=GameType::create($d);
            foreach($gameCategories as $cat){
                $gameType->categories()->create($cat);
            }
        }
    }
}
