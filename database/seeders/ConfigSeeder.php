<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('configs')->insert([
            'key' => 'competition_scale',
            'value' => '[{"value":"INTL","label":"International"},{"value":"REGION","label":"Region"},{"value":"LOCAL","label":"Local"}]',
        ]);
        DB::table('configs')->insert([
            'key' => 'languages',
            'value' => '[{"value":"en","label":"English"},{"value":"pt","label":"Portugues"},{"value":"zh_CN","label":"Chinese (Simplify)"},{"value":"zh_TW","label":"Chinese (Traditional)"}]',
        ]);
    }
}
