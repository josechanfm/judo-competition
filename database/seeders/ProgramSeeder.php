<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->insert([
            'competition_id'=>1,
            'sequence'=>1,
            'date'=>'2023-04-15',
            'category_group'=>'A組',
            'weight_group'=>'男子55公斤以下',
            'mat'=>'1',
            'section'=>'1',
            'contest_system'=>'em',
            'chart_size'=>'4',
            'duration'=>'00:04:00',
            'status'=>0
        ]);
        DB::table('programs')->insert([
            'competition_id'=>1,
            'sequence'=>2,
            'date'=>'2023-04-15',
            'category_group'=>'A組',
            'weight_group'=>'男子60公斤以下',
            'mat'=>'1',
            'section'=>'1',
            'contest_system'=>'em',
            'chart_size'=>'8',
            'duration'=>'00:04:00',
            'status'=>0
        ]);
        DB::table('programs')->insert([
            'competition_id'=>1,
            'sequence'=>3,
            'date'=>'2023-04-15',
            'category_group'=>'A組',
            'weight_group'=>'男子65公斤以下',
            'mat'=>'1',
            'section'=>'1',
            'contest_system'=>'em',
            'chart_size'=>'16',
            'duration'=>'00:04:00',
            'status'=>0
        ]);
        DB::table('programs')->insert([
            'competition_id'=>1,
            'sequence'=>4,
            'date'=>'2023-04-15',
            'category_group'=>'A組',
            'weight_group'=>'男子72公斤以下',
            'mat'=>'1',
            'section'=>'1',
            'contest_system'=>'em',
            'chart_size'=>'32',
            'duration'=>'00:04:00',
            'status'=>0
        ]);
        DB::table('programs')->insert([
            'competition_id'=>1,
            'sequence'=>5,
            'date'=>'2023-04-15',
            'category_group'=>'A組',
            'weight_group'=>'男子78公斤以下',
            'mat'=>'1',
            'section'=>'1',
            'contest_system'=>'em',
            'chart_size'=>'64',
            'duration'=>'00:04:00',
            'status'=>0
        ]);


    }
}
