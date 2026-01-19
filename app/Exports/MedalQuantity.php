<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MedalQuantity implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $competition;
    protected $programs;
    protected $rowNumber = 1;
    protected $totalParticipants = 0;
    protected $totalGold = 0;
    protected $totalSilver = 0;
    protected $totalBronze = 0;
    
    public function __construct($competition)
    {
        $this->competition = $competition;
        $this->programs = $competition->programs; // 直接获取集合
    }
    
    public function collection()
    {
        return $this->programs; // 直接返回集合
    }
    
    public function headings(): array
    {
        return [
            'NO',
            '比賽日期',
            '参賽組別',
            '比賽人數',
            '参賽級別',
            '金',
            '銀',
            '銅'
        ];
    }
    
    public function map($program): array
    {
        // 获取比赛人数
        $participantCount = $program->athletes->count();
        
        // 根据比赛人数计算奖牌数量
        list($gold, $silver, $bronze) = $this->calculateMedals($participantCount);
        
        // 累加總計
        $this->totalParticipants += $participantCount;
        $this->totalGold += (int)$gold;
        $this->totalSilver += (int)$silver;
        $this->totalBronze += (int)$bronze;
        
        return [
            $this->rowNumber++, // 按顺序编号，从1开始
            $program->competition_date ?? $program->date ?? '', // 比赛日期
            $program->converGender() . $program->competitionCategory->name ?? '', // 参赛组别
            $participantCount,
            $program->convertWeight() , // 参赛级别
            $gold,
            $silver,
            $bronze
        ];
    }
    
    /**
     * 根据比赛人数计算奖牌数量
     */
    protected function calculateMedals($participantCount)
    {
        if($this->competition->competition_type->awarding_methods){
            if ($participantCount >= 4) {
                return ['1', '1', '2']; // 1金, 1银, 2铜
            } elseif ($participantCount == 3) {
                return ['1', '1', '1']; // 1金, 1银, 1铜
            } elseif ($participantCount == 2) {
                return ['1', '1', '0']; // 1金, 1银, 0铜
            } else {
                return ['0', '0', '0']; // 其他情况无奖牌
            }
        }else {
            if ($participantCount >= 5) {
                return ['1', '1', '2']; // 1金, 1银, 2铜
            } elseif ($participantCount == 4) {
                return ['1', '1', '1']; // 1金, 1银, 1铜
            } elseif ($participantCount == 3) {
                return ['1', '1', '0']; // 1金, 1银, 0铜
            } elseif ($participantCount == 2) {
                return ['1', '0', '0']; // 其他情况无奖牌
            } else {
                return ['0', '0', '0']; 
            }
        }

    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // 設定標題行樣式
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // 獲取數據行數
                $lastRow = $this->programs->count() + 1;
                
                // 添加總計行
                $event->sheet->setCellValue('A' . ($lastRow + 1), '總計');
                $event->sheet->setCellValue('D' . ($lastRow + 1), $this->totalParticipants);
                $event->sheet->setCellValue('F' . ($lastRow + 1), $this->totalGold);
                $event->sheet->setCellValue('G' . ($lastRow + 1), $this->totalSilver);
                $event->sheet->setCellValue('H' . ($lastRow + 1), $this->totalBronze);
                
                // 設定總計行樣式
                $event->sheet->getStyle('A' . ($lastRow + 1) . ':H' . ($lastRow + 1))->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']],
                    'alignment' => ['horizontal' => 'center']
                ]);
                
                // 合併總計標題的單元格
                $event->sheet->mergeCells('A' . ($lastRow + 1) . ':C' . ($lastRow + 1));
                $event->sheet->getStyle('A' . ($lastRow + 1))->getAlignment()->setHorizontal('center');
            },
        ];
    }
}