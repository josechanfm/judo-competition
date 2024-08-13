<?php

namespace Database\Seeders;

use App\Models\GameCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Competition;
use App\Models\GameType;
use App\Models\Athlete;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $programs=[
            [
                'sequence' => 1,
                'date' => '2024-04-15',
                'weight_code' => 'MW42-',
                'mat' => '1',
                'section' => '1',
                'contest_system' => 'erm',
                'chart_size' => '4',
                'duration' => 240,
                'status' => 0
            ],[
                'sequence' => 1,
                'date' => '2024-04-15',
                'weight_code' => 'MW55-',
                'mat' => '1',
                'section' => '1',
                'contest_system' => 'erm',
                'chart_size' => '4',
                'duration' => 240,
                'status' => 0
            ],[
                'sequence' => 2,
                'date' => '2024-04-15',
                'weight_code' => 'MW60-',
                'mat' => '1',
                'section' => '1',
                'contest_system' => 'erm',
                'chart_size' => '8',
                'duration' => 240,
                'status' => 0
            ],[
                'sequence' => 3,
                'weight_code' => 'MW65-',
                'date' => '2024-04-15',
                'mat' => '1',
                'section' => '1',
                'contest_system' => 'erm',
                'chart_size' => '16',
                'duration' => 240,
                'status' => 0
            ],[
                'sequence' => 4,
                'date' => '2024-04-15',
                'weight_code' => 'MW72-',
                'mat' => '1',
                'section' => '1',
                'contest_system' => 'erm',
                'chart_size' => '32',
                'duration' => 240,
                'status' => 0
            ],[
                'sequence' => 5,
                'date' => '2024-04-15',
                'weight_code' => 'MW78-',
                'mat' => '1',
                'section' => '1',
                'contest_system' => 'erm',
                'chart_size' => '64',
                'duration' => 240,
                'status' => 0
            ]
        ];

        $competition=Competition::create([
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
        $athletes=Athlete::factory(1)->count(200)->create(['competition_id' => $competition->id, 'team_id' => 1]);
        $athleteId=0;
        $gameType=GameType::find(1);
        $competition->competition_type()->create($gameType->toArray());
        $gameCategories=$gameType->categories;
        foreach($gameCategories as $cat){
            $c=$cat->toArray();
            unset($c['game_type_id']);
            $competitionCategory=$competition->categories()->create($c);
            foreach($programs as $program){
                $pro=$competitionCategory->programs()->create($program);
                $pro->programAthletes()->create($athletes[$athleteId++]);
            }
        }
        
    }
}
