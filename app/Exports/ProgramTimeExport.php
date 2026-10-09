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
    protected $venueAPrograms = [];
    protected $venueBPrograms = [];
    protected $venueAGroupedPrograms = [];
    protected $venueBGroupedPrograms = [];

    /**
     * 項目統計快取：program id => athletes / matches / match_seconds / total_seconds / group_type / label
     */
    protected $stats = [];

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
        // 确保按 competitioncategory 正确排序
        // 注意：不能用 ->with('athletes')。Program::getAthletesAttribute() 覆寫了 athletes
        // 屬性存取，Eloquent 會優先走 accessor（每次重新查 SQL），eager load 等於無效，
        // 改用 withCount，統一由 computeProgramStats() 讀 athletes_count。
        $this->programs = $competition->programs()
            ->with(['competitionCategory'])
            ->withCount('athletes')
            ->orderBy('competition_category_id')
            ->get();
        $this->initializeTimeSlots();
        $this->computeProgramStats();
        $this->simpleAllocatePrograms(); // 改用簡單的平均分配
        $this->organizeByVenue();
        $this->groupProgramsByCategory();
    }

    /**
     * 先把每個項目的統計資料算好。
     *
     * 這些值（場數、單場時間、總時間、組別、顯示名稱）在排序、分場地、
     * 產出列資料與小計時會反覆用到，原本每次都重算，效能很差
     * （實測 64 個項目會發出 4000+ 次查詢）。
     */
    private function computeProgramStats(): void
    {
        foreach ($this->programs as $program) {
            $athletesCount = (int) ($program->athletes_count ?? 0);
            $matchSeconds = (int) ($program->duration ?? 0);
            $matchesCount = $this->calculateMatchesCount($athletesCount, $program->competition_system);

            $this->stats[$program->id] = [
                'athletes' => $athletesCount,
                'matches' => $matchesCount,
                'match_seconds' => $matchSeconds,
                'total_seconds' => $matchesCount * $matchSeconds,
                'group_type' => $this->getGroupType($program),
                'label' => $program->convertGender() . $program->competitionCategory->name . $program->convertWeight(),
            ];
        }
    }

    private function stats($program): array
    {
        return $this->stats[$program->id];
    }

    private function totalSeconds($program): int
    {
        return $this->stats[$program->id]['total_seconds'];
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
     * 依組別把項目分配到兩個場地。
     *
     * 同一個組別內，先按「項目總時間」由大到小排序，再依序丟給目前總時間較少的場地
     * （LPT / Longest Processing Time first），讓兩個場地的總時間盡量接近。
     * 原本的做法是另外限制「A 場地只能放 ceil(n/2) 個項目」，反而讓兩邊比較不平衡。
     */
    private function simpleAllocatePrograms(): void
    {
        foreach ($this->programs->groupBy('competition_category_id') as $programs) {
            $venueATotalTime = 0;
            $venueBTotalTime = 0;

            // 時間長的先放，平衡效果最好
            foreach ($programs->sortByDesc(fn ($program) => $this->totalSeconds($program)) as $program) {
                $programTime = $this->totalSeconds($program);

                if ($venueATotalTime <= $venueBTotalTime) {
                    $venueATotalTime += $programTime;
                    $this->allocateToVenueSimple($program, '場地A', $programTime);
                } else {
                    $venueBTotalTime += $programTime;
                    $this->allocateToVenueSimple($program, '場地B', $programTime);
                }
            }
        }
    }

    /**
     * 简单分配到场地
     *
     * $totalSeconds 是整個項目的總時間（場數 × 單場時間），不是單場時間。
     * 注意：這裡記錄的 start_time / end_time 目前 Excel 並沒有輸出，只有 venue 會被讀取。
     */
    private function allocateToVenueSimple($program, string $venue, int $totalSeconds): void
    {
        // 根据组别决定时段
        $groupType = $this->stats($program)['group_type'];
        $timeSlot = ($groupType === '兒童組') ? 'morning' : 'afternoon';
        
        $startTime = $this->timeSlots[$timeSlot]['start'];
        $endTime = $this->calculateEndTime($startTime, $totalSeconds);

        $this->venueAllocations[$program->id] = [
            'time_slot_name' => $this->timeSlots[$timeSlot]['name'],
            'venue' => $venue,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $totalSeconds
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
            $venueARow = isset($venueAData[$i]) ? $venueAData[$i] : array_fill(0, 6, '');
            $venueBRow = isset($venueBData[$i]) ? $venueBData[$i] : array_fill(0, 6, '');
            
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
                $stats = $this->stats($program);
                $categoryAthletesTotal += $stats['athletes'];
                $categoryMatchesTotal += $stats['matches'];
                $categoryDurationTotal += $stats['total_seconds'];
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
            $data[] = array_fill(0, 6, '');
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
        $stats = $this->stats($program);

        return [
            $stats['label'],
            $this->getContestSystemName($program->competition_system),
            $stats['athletes'],
            $this->formatTime($stats['match_seconds']),
            $stats['matches'],
            $this->formatTime($stats['total_seconds']),
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

    private function calculateEndTime(string $startTime, int $durationSeconds): string
    {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = $startTimestamp + $durationSeconds;

        // 原本寫 date('F:i:s')，F 是「英文月份全名」而非時，會產生 "September:05:00" 這種值
        return date('H:i:s', $endTimestamp);
    }

    private function getGroupType($program): string
    {
        $categoryName = $program->competitionCategory->name ?? '';

        // 「青少年」也包含「少年」，所以不需要額外的分支（原本那個 elseif 永遠不會執行）
        return match (true) {
            str_contains($categoryName, '兒童') => '兒童組',
            str_contains($categoryName, '少年') => '少年組',
            default => '公開組',
        };
    }

    private function calculateMatchesCount(int $athletesCount, ?string $contestSystem): int
    {
        // 沒有（或只有一位）運動員的項目不應該產生場次，
        // 否則 kos 會得到 -1、erm 會得到 3，把小計帶成負數
        if ($athletesCount < 2) {
            return 0;
        }

        return match ($contestSystem) {
            'kos' => $athletesCount - 1,                                   // 單淘汰
            'rrb' => intdiv($athletesCount * ($athletesCount - 1), 2),      // 循環賽
            'erm' => $athletesCount - 1 + 4,                                // 8 強復活賽（雙敗淘汰制）
            default => 0,
        };
    }

    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    private function getContestSystemName(?string $contestSystem): string
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