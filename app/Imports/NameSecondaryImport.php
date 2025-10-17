<?php

namespace App\Imports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class NameSecondaryImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // 跳过标题行
        $rows->shift();
        
        foreach ($rows as $row) {
            $name = $row[0]; // Excel 第一列是 name
            $nameSecondary = $row[1]; // Excel 第二列是 name_secondary
            
            Athlete::where('name', $name)
                    ->update(['name_secondary' => $nameSecondary]);
        }
    }
}