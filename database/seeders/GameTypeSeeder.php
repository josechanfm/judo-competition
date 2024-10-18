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
                'name' => 'IJF',
                'name_secondary' => 'IJF',
                'code'=>'IJF',
                'awarding_methods'=>'0',
                'language'=>'en',
                'is_language_secondary_enabled'=>0,
                'language'=>'en',
                'game_categories'=>[
                    [
                        'name' => 'Senior and Junior',
                        'name_secondary' => 'Untitled',
                        'code' => 'SJ',
                        'weights' => [
                            "FW45-","FW52-","FW57-","FW63-","FW70-","FW78-","FW78+","FWOPEN",
                            "MW60-","MW66-","MW73-","MW81-","MW90-","MW100-","MW100+","MWOPEN"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Cadet under 18 years',
                        'name_secondary' => 'Untitled',
                        'code' => 'CU18',
                        'weights' => [
                            "FW40-","FW44-","FW48-","FW52-","FW57-","FW63-","FW70-","FW70+",
                            "MW50-","MW55-","MW60-","MW66-","MW73-","MW81-","MW90-","MW90+"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Senior and Junior Mixed',
                        'name_secondary' => 'Untitled',
                        'code' => 'SJM',
                        'weights' => [
                            "FW57-","FW70-","FW70+",
                            "MW73-","MW90-","MW90+"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Senior Mixed Olympic Games',
                        'name_secondary' => 'Untitled',
                        'code' => 'SMOG',
                        'weights' => [
                            "FW57-","FW70-","FW70+",
                            "MW73-","MW90-","MW90+"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Cadet Mixed',
                        'name_secondary' => 'Untitled',
                        'code' => 'CM',
                        'weights' => [
                            "FW48-","FW63-","FW63+",
                            "MW60-","MW81-","MW81+"
                        ],
                        'duration' => 240
                    ]
                ]
            ],[
                'name' => '學界賽',
                'name_secondary' => 'COMPEONATO SESCOLAR',
                'code'=>'CEJ',
                'awarding_methods'=>'0',
                'language'=>'zh_TW',
                'is_language_secondary_enabled'=>1,
                'language'=>'pt',
                'game_categories'=>[
                    [
                        'name' => 'A組',
                        'name_secondary' => 'Untitled',
                        'code' => 'A',
                        'weights' => ["MW55-","MW60-","FW48-"],
                        'duration' => 240
                    ]
                ]
            ],[
                'name' => '公開賽',
                'code'=>'OPEN',
                'awarding_methods'=>'1',
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
