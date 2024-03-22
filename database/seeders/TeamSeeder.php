<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('teams')->insert([
            'competition_id' => 1,
            'name_zh' => '柔道隊',
            'name_pt' => 'Judo Team',
            'abbreviation' => 'CST',
        ]);
    }
}
