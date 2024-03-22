<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('game_categories')->insert([
            'game_type_id' => '1',
            'name' => 'A組',
            'name_secondary' => 'Untitled',
            'code' => 'A',
            'weights' => '["MW55-","MW60-","FW48-"]',
            'duration' => 240
        ]);
    }
}
