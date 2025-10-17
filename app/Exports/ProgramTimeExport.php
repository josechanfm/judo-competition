<?php
namespace App\Exports;

use App\Models\Competition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProgramTimeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $competition;
    protected $programs;
    protected $timeSlots = [];
    protected $venueAllocations = [];
    protected $venueSchedules = [];
    protected $venueLoad = [];
    protected $venueAPrograms = [];
    protected $venueBPrograms = [];
    protected $venueAGroupedPrograms = [];
    protected $venueBGroupedPrograms = [];

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
        // 确保按 competitioncategory 正确排序
        $this->programs = $competition->programs()
            ->with(['competitionCategory', 'athletes'])
            ->orderBy('competition_category_id')
            ->get();
        $this->initializeTimeSlots();
        $this->simpleAllocatePrograms(); // 改用簡單的平均分配
        $this->organizeByVenue();
        $this->groupProgramsByCategory();
    }

    public function collection()
    {
        // 返回合并后的集合，左侧场地A，右侧场地B
        return collect($this->prepareCombinedData());
    }

    public function headings(): array
    {
        return [
            // 左侧场地A的标题
            '賽事名稱',
            '比賽形式',
            '運動員數量',
            '每場比賽時間',
            '總比賽場數',
            '總比賽時間',
            
            // 分隔列
            '',
            
            // 右侧场地B的标题
            '賽事名稱',
            '比賽形式',
            '運動員數量',
            '每場比賽時間',
            '總比賽場數',
            '總比賽時間',
        ];
    }

    public function map($row): array
    {
        // 返回组合好的行数据
        return $row;
    }

    /**
     * 简单平均分配项目到场地
     */
    private function simpleAllocatePrograms(): void
    {
        // 先按 competition_category_id 分组
        $groupedPrograms = [];
        foreach ($this->programs as $program) {
            $categoryId = $program->competition_category_id;
            if (!isset($groupedPrograms[$categoryId])) {
                $groupedPrograms[$categoryId] = [];
            }
            $groupedPrograms[$categoryId][] = $program;
        }

        // 对每个分组内的项目进行平均分配
        foreach ($groupedPrograms as $categoryId => $programs) {
            $totalPrograms = count($programs);
            $venueAPrograms = [];
            $venueBPrograms = [];
            
            // 计算每个场地应该分配的项目数量
            $venueACount = ceil($totalPrograms / 2);
            $venueBCount = $totalPrograms - $venueACount;
            
            // 按项目总时间排序，大的先分配
            usort($programs, function($a, $b) {
                $aSeconds = $this->calculateTotalSeconds($a);
                $bSeconds = $this->calculateTotalSeconds($b);
                return $bSeconds - $aSeconds;
            });
            
            // 交替分配到两个场地，尽量保持时间平衡
            $venueATotalTime = 0;
            $venueBTotalTime = 0;
            
            foreach ($programs as $program) {
                $programTime = $this->calculateTotalSeconds($program);
                
                if (count($venueAPrograms) < $venueACount && 
                    (count($venueBPrograms) >= $venueBCount || $venueATotalTime <= $venueBTotalTime)) {
                    $venueAPrograms[] = $program;
                    $venueATotalTime += $programTime;
                    $this->allocateToVenueSimple($program, '場地A', $programTime);
                } else {
                    $venueBPrograms[] = $program;
                    $venueBTotalTime += $programTime;
                    $this->allocateToVenueSimple($program, '場地B', $programTime);
                }
            }
        }
    }

    /**
     * 简单分配到场地
     */
    private function allocateToVenueSimple($program, string $venue, int $duration): void
    {
        // 根据组别决定时段
        $groupType = $this->getGroupType($program);
        $timeSlot = ($groupType === '兒童組') ? 'morning' : 'afternoon';
        
        $startTime = $this->timeSlots[$timeSlot]['start'];
        $endTime = $this->calculateEndTime($startTime, $duration);

        $this->venueAllocations[$program->id] = [
            'time_slot_name' => $this->timeSlots[$timeSlot]['name'],
            'venue' => $venue,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration
        ];
    }

    /**
     * 准备组合数据 - 左侧场地A，右侧场地B
     */
    private function prepareCombinedData(): array
    {
        $combinedData = [];
        
        // 准备场地A的数据（包含分组和总计）
        $venueAData = $this->prepareVenueDataWithTotals($this->venueAGroupedPrograms, 'A');
        // 准备场地B的数据（包含分组和总计）
        $venueBData = $this->prepareVenueDataWithTotals($this->venueBGroupedPrograms, 'B');
        
        $maxRows = max(count($venueAData), count($venueBData));
        
        for ($i = 0; $i < $maxRows; $i++) {
            $venueARow = isset($venueAData[$i]) ? $venueAData[$i] : array_fill(0, 8, '');
            $venueBRow = isset($venueBData[$i]) ? $venueBData[$i] : array_fill(0, 8, '');
            
            // 组合行：左侧场地A + 空分隔列 + 右侧场地B
            $combinedRow = array_merge($venueARow, [''], $venueBRow);
            $combinedData[] = $combinedRow;
        }
        
        return $combinedData;
    }

    /**
     * 准备场地数据（包含分组和总计）
     */
    private function prepareVenueDataWithTotals($groupedPrograms, $venue): array
    {
        $data = [];
        
        foreach ($groupedPrograms as $categoryId => $programs) {
            $category = $programs[0]->competitionCategory;
            $categoryName = $category->name;
            
            // 添加分类标题行
            $data[] = [
                $categoryName,
                '', '', '', '', '',
            ];
            
            $categoryAthletesTotal = 0;
            $categoryMatchesTotal = 0;
            $categoryDurationTotal = 0;
            
            // 添加项目数据
            foreach ($programs as $program) {
                $programRow = $this->prepareProgramRow($program);
                $data[] = $programRow;
                
                // 累加统计
                $categoryAthletesTotal += $program->athletes->count();
                $categoryMatchesTotal += $this->calculateMatchesCount($program->athletes->count(), $program->competition_system);
                $categoryDurationTotal += $this->calculateTotalSeconds($program);
            }
            
            // 添加分类总计行
            $data[] = [
                '總計',
                '',
                $categoryAthletesTotal,
                '',
                $categoryMatchesTotal,
                $this->formatTime($categoryDurationTotal),
            ];
            
            // 添加空行分隔
            $data[] = array_fill(0, 8, '');
        }
        
        return $data;
    }

    /**
     * 按 competition_category_id 分组项目
     */
    private function groupProgramsByCategory(): void
    {
        $this->venueAGroupedPrograms = [];
        $this->venueBGroupedPrograms = [];
        
        // 分组场地A的项目
        foreach ($this->venueAPrograms as $program) {
            $categoryId = $program->competition_category_id;
            if (!isset($this->venueAGroupedPrograms[$categoryId])) {
                $this->venueAGroupedPrograms[$categoryId] = [];
            }
            $this->venueAGroupedPrograms[$categoryId][] = $program;
        }
        
        // 分组场地B的项目
        foreach ($this->venueBPrograms as $program) {
            $categoryId = $program->competition_category_id;
            if (!isset($this->venueBGroupedPrograms[$categoryId])) {
                $this->venueBGroupedPrograms[$categoryId] = [];
            }
            $this->venueBGroupedPrograms[$categoryId][] = $program;
        }
        
        // 按 category_id 排序
        ksort($this->venueAGroupedPrograms);
        ksort($this->venueBGroupedPrograms);
    }

    /**
     * 准备单个项目的行数据
     */
    private function prepareProgramRow($program): array
    {
        $athletesCount = $program->athletes->count();
        $matchDurationSeconds = $program->duration;
        $contestSystem = $program->competition_system;
        
        $matchesCount = $this->calculateMatchesCount($athletesCount, $contestSystem);
        $totalSeconds = $matchesCount * $matchDurationSeconds;
        
        $matchTimeFormatted = $this->formatTime($matchDurationSeconds);
        $totalTimeFormatted = $this->formatTime($totalSeconds);
        
        $allocation = $this->getProgramAllocation($program);
        $groupType = $this->getGroupType($program);

        return [
            $program->converGender() . $program->competitionCategory->name . $program->convertWeight(),
            $this->getContestSystemName($contestSystem),
            $athletesCount,
            $matchTimeFormatted,
            $matchesCount,
            $totalTimeFormatted,
        ];
    }

    /**
     * 按场地组织项目
     */
    private function organizeByVenue(): void
    {
        $this->venueAPrograms = [];
        $this->venueBPrograms = [];
        
        // 根据分配结果组织项目
        foreach ($this->programs as $program) {
            $allocation = $this->getProgramAllocation($program);
            $venue = $allocation['venue'] ?? '';
            
            if ($venue === '場地A') {
                $this->venueAPrograms[] = $program;
            } elseif ($venue === '場地B') {
                $this->venueBPrograms[] = $program;
            }
        }
        
        // 按 competition_category_id 排序
        if (!empty($this->venueAPrograms)) {
            $this->venueAPrograms = collect($this->venueAPrograms)
                ->sortBy('competition_category_id')
                ->values()
                ->all();
        }
        
        if (!empty($this->venueBPrograms)) {
            $this->venueBPrograms = collect($this->venueBPrograms)
                ->sortBy('competition_category_id')
                ->values()
                ->all();
        }
    }

    // 以下方法保持不变...
    private function initializeTimeSlots(): void
    {
        $this->timeSlots = [
            'morning' => [
                'name' => '上午',
                'start' => '09:00:00',
                'end' => '12:00:00',
                'duration' => 3 * 3600,
                'venues' => ['場地A', '場地B'],
                'allowed_groups' => ['兒童組']
            ],
            'afternoon' => [
                'name' => '下午',
                'start' => '13:00:00',
                'end' => '17:00:00',
                'duration' => 4 * 3600,
                'venues' => ['場地A', '場地B'],
                'allowed_groups' => ['少年組', '公開組']
            ]
        ];
    }

    private function getProgramAllocation($program): ?array
    {
        return $this->venueAllocations[$program->id] ?? null;
    }

    private function calculateTotalSeconds($program): int
    {
        $athletesCount = $program->athletes->count();
        $contestSystem = $program->competition_system;
        $matchesCount = $this->calculateMatchesCount($athletesCount, $contestSystem);
        
        return $matchesCount * $program->duration;
    }

    private function calculateEndTime(string $startTime, int $durationSeconds): string
    {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = $startTimestamp + $durationSeconds;
        return date('F:i:s', $endTimestamp);
    }

    private function getGroupType($program): string
    {
        $categoryName = $program->competitionCategory->name ?? '';
        
        if (strpos($categoryName, '兒童') !== false) {
            return '兒童組';
        } elseif (strpos($categoryName, '少年') !== false) {
            return '少年組';
        } elseif (strpos($categoryName, '青少年') !== false) {
            return '少年組';
        } else {
            return '公開組';
        }
    }

    private function calculateMatchesCount(int $athletesCount, string $contestSystem): int
    {
        switch ($contestSystem) {
            case 'kos': return $athletesCount - 1;
            case 'rrb': return ($athletesCount * ($athletesCount - 1)) / 2;
            case 'erm': return $athletesCount <= 8 ? 15 : (int) round($athletesCount * 1.8);
            default: return 0;
        }
    }

    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    private function getContestSystemName(string $contestSystem): string
    {
        $systems = [
            'kos' => '單淘汰賽',
            'rrb' => '循環賽',
            'erm' => '8強復活賽'
        ];
        
        return $systems[$contestSystem] ?? '未知賽制';
    }

    public function styles(Worksheet $sheet)
    {
        // 获取最大行数（需要重新计算因为添加了分组行和总计行）
        $venueARows = 0;
        foreach ($this->venueAGroupedPrograms as $programs) {
            $venueARows += count($programs) + 3; // 项目数 + 标题行 + 总计行 + 空行
        }
        
        $venueBRows = 0;
        foreach ($this->venueBGroupedPrograms as $programs) {
            $venueBRows += count($programs) + 3; // 项目数 + 标题行 + 总计行 + 空行
        }
        
        $maxRow = max($venueARows, $venueBRows) + 1;
        
        // 设置标题行样式
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $sheet->getStyle('H1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ED7D31']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        // 设置分隔列样式
        $sheet->getStyle('G1:G' . $maxRow)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']]
        ]);
        
        // 设置边框
        $sheet->getStyle('A1:M' . $maxRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // 设置分组标题行样式
        $currentRow = 2;
        foreach ($this->venueAGroupedPrograms as $programs) {
            $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E6E6']]
            ]);
            $currentRow += count($programs) + 3;
        }
        
        $currentRow = 2;
        foreach ($this->venueBGroupedPrograms as $programs) {
            $sheet->getStyle('H' . $currentRow . ':M' . $currentRow)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E6E6']]
            ]);
            $currentRow += count($programs) + 3;
        }
        
        // 设置总计行样式
        $currentRow = 2;
        foreach ($this->venueAGroupedPrograms as $programs) {
            $totalRow = $currentRow + count($programs) + 1;
            $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']]
            ]);
            $currentRow += count($programs) + 3;
        }
        
        $currentRow = 2;
        foreach ($this->venueBGroupedPrograms as $programs) {
            $totalRow = $currentRow + count($programs) + 1;
            $sheet->getStyle('H' . $totalRow . ':M' . $totalRow)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']]
            ]);
            $currentRow += count($programs) + 3;
        }
        
        // 设置自动换行
        $sheet->getStyle('A:M')->getAlignment()->setWrapText(true);
        
        return [];
    }

    public function title(): string
    {
        return '賽程時間表';
    }
}