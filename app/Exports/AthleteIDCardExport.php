<?php
namespace App\Exports;

use App\Models\Competition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AthleteIDCardExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $athletes;

    public function __construct($athletes)
    {
        $this->athletes = $athletes;
    }

    public function collection()
    {
        return $this->athletes;
    }

    public function headings(): array
    {
        return [
            '學校名稱',
            '學校代號',
            '運動員中文名',
            '運動員葡文名',
            '參賽項目',
        ];
    }

    public function map($athlete): array
    {
        return [
            $athlete->team->name ?? '', // 學校名稱
            $athlete->team->abbreviation ?? '', // 學校代號
            $athlete->name ?? '', // 運動員中文名
            $athlete->name_secondary ?? '', // 運動員葡文名
            $athlete->programCategoryWeight ?? '', // 參賽項目
        ];
    }

    public function title(): string
    {
        return '運動員ID卡資料';
    }
}