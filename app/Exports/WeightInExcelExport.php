<?php

namespace App\Exports;

use App\Models\Competition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * 匯出整場賽事的過磅資料（所有組別 / 所有項目），供工作人員填寫體重後再匯入。
 *
 * 欄位標題同時是 WeightInImport 讀取用的 key（WeightInImport 用 HeadingRowFormatter::none
 * 保留中文標題），所以標題文字請勿隨意更動。
 */
class WeightInExcelExport implements FromCollection, WithHeadings, WithColumnWidths, WithTitle
{
    public const HEADINGS = [
        '選手ID',
        '組別',
        '公斤級',
        '參賽項目',
        '隊伍',
        '姓名',
        '外文姓名',
        '種子',
        '體重(kg)',
        '過磅結果',
    ];

    public function __construct(private Competition $competition)
    {
    }

    public function collection()
    {
        $programs = $this->competition->programs()
            ->with(['competitionCategory', 'programAthletes.athlete.team'])
            ->orderByCategoryAndWeightGroup()
            ->get();

        $rows = collect();

        foreach ($programs as $program) {
            $categoryName = $program->competitionCategory->name ?? '';
            $programLabel = trim($program->convertGender() . $program->convertWeight());

            foreach ($program->programAthletes as $programAthlete) {
                $athlete = $programAthlete->athlete;

                $rows->push([
                    $programAthlete->id,
                    $categoryName,
                    $program->weight_code,
                    $programLabel,
                    $athlete->team->name ?? '',
                    $athlete->name ?? '',
                    $athlete->name_secondary ?? '',
                    $programAthlete->seed ?: '',
                    $programAthlete->weight,
                    $this->resultLabel($programAthlete->is_weight_passed),
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return self::HEADINGS;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 14,
            'C' => 12,
            'D' => 18,
            'E' => 24,
            'F' => 16,
            'G' => 22,
            'H' => 8,
            'I' => 12,
            'J' => 12,
        ];
    }

    public function title(): string
    {
        return '過磅資料';
    }

    private function resultLabel($isWeightPassed): string
    {
        return match ((string) $isWeightPassed) {
            '1' => '通過',
            '0' => '不通過',
            default => '未過磅',
        };
    }
}
