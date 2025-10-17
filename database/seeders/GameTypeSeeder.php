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
        $data = [
            [
                'name' => 'IJF',
                'name_secondary' => 'IJF',
                'code' => 'IJF',
                'awarding_methods' => '0',
                'language' => 'en',
                'is_language_secondary_enabled' => 0,
                'language' => 'en',
                'game_categories' => [
                    [
                        'name' => 'Senior and Junior',
                        'name_secondary' => 'Untitled',
                        'code' => 'SJ',
                        'weights' => [
                            "FW45-",
                            "FW52-",
                            "FW57-",
                            "FW63-",
                            "FW70-",
                            "FW78-",
                            "FW78+",
                            "FWOPEN",
                            "MW60-",
                            "MW66-",
                            "MW73-",
                            "MW81-",
                            "MW90-",
                            "MW100-",
                            "MW100+",
                            "MWOPEN"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Cadet under 18 years',
                        'name_secondary' => 'Untitled',
                        'code' => 'CU18',
                        'weights' => [
                            "FW40-",
                            "FW44-",
                            "FW48-",
                            "FW52-",
                            "FW57-",
                            "FW63-",
                            "FW70-",
                            "FW70+",
                            "MW50-",
                            "MW55-",
                            "MW60-",
                            "MW66-",
                            "MW73-",
                            "MW81-",
                            "MW90-",
                            "MW90+"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Senior and Junior Mixed',
                        'name_secondary' => 'Untitled',
                        'code' => 'SJM',
                        'weights' => [
                            "FW57-",
                            "FW70-",
                            "FW70+",
                            "MW73-",
                            "MW90-",
                            "MW90+"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Senior Mixed Olympic Games',
                        'name_secondary' => 'Untitled',
                        'code' => 'SMOG',
                        'weights' => [
                            "FW57-",
                            "FW70-",
                            "FW70+",
                            "MW73-",
                            "MW90-",
                            "MW90+"
                        ],
                        'duration' => 240
                    ],
                    [
                        'name' => 'Cadet Mixed',
                        'name_secondary' => 'Untitled',
                        'code' => 'CM',
                        'weights' => [
                            "FW48-",
                            "FW63-",
                            "FW63+",
                            "MW60-",
                            "MW81-",
                            "MW81+"
                        ],
                        'duration' => 240
                    ]
                ]
            ],
            [
                'name' => '學界賽',
                'name_secondary' => 'COMPEONATO SESCOLAR',
                'code' => 'CEJ',
                'awarding_methods' => '0',
                'language' => 'zh_TW',
                'is_language_secondary_enabled' => 1,
                'language' => 'pt',
                'game_categories' => [
                    [
                        'name' => 'A組',
                        'name_secondary' => 'Untitled',
                        'code' => 'A',
                        'weights' => ["MW55-", "MW60-", "FW48-"],
                        'duration' => 240
                    ]
                ]
            ],
            [
                'name' => '公開賽',
                'code' => 'OPEN',
                'awarding_methods' => '1',
                'language' => 'zh_TW',
                'is_language_secondary_enabled' => 0,
                'game_categories' => [
                    [
                        'name' => '元老組',
                        'name_secondary' => 'Untitled',
                        'code' => 'OLD',
                        'weights' => ["MW73-", "MW73+"],
                        'duration' => 240
                    ],
                    [
                        'name' => '先進組',
                        'name_secondary' => 'Untitled',
                        'code' => 'AVC',
                        'weights' => ["MW73-", "MW73+"],
                        'duration' => 240
                    ],
                    [
                        'name' => '公開組',
                        'name_secondary' => 'Untitled',
                        'code' => 'OPEN',
                        'weights' => ["MW60-", "MW66-", "MW73-", "MW81-", "MW90-", "MWULW", "FW48-", "FW52-", "FW57-", "FW63-", "FW70-", "FWULW"],
                        'duration' => 240
                    ],
                    [
                        'name' => '少年A組',
                        'name_secondary' => 'Untitled',
                        'code' => 'A',
                        'weights' => ["MW45-", "MW50-", "MW55-", "MW60-", "MW66-", "MW73-", "MW73+", "FW44-", "FW48-", "FW52-", "FW57-", "FW63-", "FW70-", "FW70+"],
                        'duration' => 180
                    ],
                    [
                        'name' => '少年B組',
                        'name_secondary' => 'Untitled',
                        'code' => 'B',
                        'weights' => ["MW42-", "MW46-", "MW50-", "MW55-", "MW60-", "MW66-", "MW66+", "FW40-", "FW44-", "FW48-", "FW52-", "FW57-", "FW63-", "FW63+"],
                        'duration' => 180
                    ],
                    [
                        'name' => '兒童C組',
                        'name_secondary' => 'Untitled',
                        'code' => 'C',
                        'weights' => ["MW27-", "MW30-", "MW34-", "MW38-", "MW42-", "MW46-", "MW50-", "MW55-", "MW60-", "MW60+", "FW25-", "FW28-", "FW32-", "FW36-", "FW40-", "FW44-", "FW48-", "FW52-", "FW57-", "FW57+"],
                        'duration' => 120
                    ],
                    [
                        'name' => '兒童D組',
                        'name_secondary' => 'Untitled',
                        'code' => 'D',
                        'weights' => ["MW24-", "MW27-", "MW30-", "MW34-", "MW38-", "MW42-", "MW46-", "MW46+", "FW22-", "FW25-", "FW28-", "FW32-", "FW36-", "FW40-", "FW44-", "FW44+"],
                        'duration' => 120
                    ],
                    [
                        'name' => '兒童E組',
                        'name_secondary' => 'Untitled',
                        'code' => 'E',
                        'weights' => ["MW21-", "MW23-", "MW25-", "MW27-", "MW30-", "MW33-", "MW33+", "FW21-", "FW23-", "FW25-", "FW27-", "FW30-", "FW33-", "FW33+", "FW52-", "FW57-", "FW57+"],
                        'duration' => 120
                    ]
                ]
            ],
            [
                'name' => '計分賽',
                'code' => 'Score',
                'awarding_methods' => '1',
                'language' => 'zh_TW',
                'is_language_secondary_enabled' => 0,
                'game_categories' => [
                    [
                        'name' => '公開組',
                        'name_secondary' => 'Untitled',
                        'code' => 'OPEN',
                        'weights' => ["MW60-", "MW66-", "MW73-", "MW81-", "MW90-", "MWULW", "FW48-", "FW52-", "FW57-", "FW63-", "FW70-", "FWULW"],
                        'duration' => 240
                    ],
                    [
                        'name' => '少年A組',
                        'name_secondary' => 'Untitled',
                        'code' => 'A',
                        'weights' => ["MW45-", "MW50-", "MW55-", "MW60-", "MW66-", "MW73-", "MW73+", "FW44-", "FW48-", "FW52-", "FW57-", "FW63-", "FW70-", "FW70+"],
                        'duration' => 240
                    ],
                    [
                        'name' => '少年B組',
                        'name_secondary' => 'Untitled',
                        'code' => 'B',
                        'weights' => ["MW42-", "MW46-", "MW50-", "MW55-", "MW60-", "MW66-", "MW66+", "FW40-", "FW44-", "FW48-", "FW52-", "FW57-", "FW63-", "FW63+"],
                        'duration' => 240
                    ],
                    [
                        'name' => '兒童C組',
                        'name_secondary' => 'Untitled',
                        'code' => 'C',
                        'weights' => ["MW27-", "MW30-", "MW34-", "MW38-", "MW42-", "MW46-", "MW50-", "MW55-", "MW60-", "MW60+", "FW25-", "FW28-", "FW32-", "FW36-", "FW40-", "FW44-", "FW48-", "FW52-", "FW57-", "FW57+"],
                        'duration' => 240
                    ]
                ]
            ]

        ];
        foreach ($data as $d) {
            $gameCategories = $d['game_categories'];
            unset($d['game_categories']);
            $gameType = GameType::create($d);
            foreach ($gameCategories as $cat) {
                $gameType->categories()->create($cat);
            }
        }
    }
}
