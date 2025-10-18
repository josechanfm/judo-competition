<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use App\Models\Bout;
use App\Models\Competition;
use Illuminate\Http\Request;
use App\Services\Printer\ProgramScheduleService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProgramScheduleController extends Controller
{
    protected $gameSheet = null;

    public function printPdf(Request $request)
    {
        $data = $request->all();

        $bouts = Bout::whereIn('id', $data['bouts'])->orderBy('sequence')->get();
        // dd($bouts);
        $inputBouts = collect($bouts)->map(function ($bout) {
            return [
                'sequence' => $bout['sequence'],
                'category' => $bout['program']->competitionCategory->name,
                'weight' => $bout->program->convertWeight(),
                'round' => $bout->bout_name,
                'event_date' => $bout->date,
                'white_player' => $bout?->white_player->name ?? '',
                'white_team' => $bout?->white_player->team->name ?? '',
                'blue_player' => $bout?->blue_player->name ?? '',
                'blue_team' => $bout?->blue_player->team->name ?? '',
                'time' => $bout->duration_formatted,
            ];
        });
        // dd($inputBouts);

        $this->gameSheet = new ProgramScheduleService();
        $this->schedule($inputBouts, $bouts[0]->mat ?? 1);
    }

    public function printAllSchedule(Competition $competition, Request $request)
    {
        $matNumbers = range(1, $competition->mat_number);
        $sectionNumbers = range(1, $competition->section_number);
        
        $allBouts = [];
        
        // 遍歷所有場地和賽區
        foreach ($matNumbers as $mat) {
            foreach ($sectionNumbers as $section) {
                // 獲取該場地和賽區的所有比賽
                $bouts = $competition->bouts()
                    ->where('mat', $mat)
                    ->where('section', $section)
                    ->where('queue', '!=', 0)
                    ->orderBy('queue')
                    ->get();
                
                if ($bouts->isNotEmpty()) {
                    $formattedBouts = $bouts->map(function ($bout) use ($mat, $section) {
                        return [
                            'sequence' => $bout['queue'],
                            'category' => $bout['program']->competitionCategory->name,
                            'weight' => $bout->program->convertWeight(),
                            'round' => $bout->bout_name,
                            'event_date' => $bout->date,
                            'white_player' => $this->smartTruncate($bout->white_player?->name ?? '待定') . $this->smartTruncate($bout->white_player?->name_secondary ?? ''),
                            'white_team' => $bout->white_player->team->name ?? '',
                            'blue_player' => $this->smartTruncate($bout?->blue_player?->name ?? '待定') . $this->smartTruncate($bout->blue_player?->name_secondary ?? ''),
                            'blue_team' => $bout?->blue_player->team->name ?? '',
                            'time' => $bout->duration_formatted,
                            'mat' => $mat,
                            'section' => $section,
                        ];
                    });
                    
                    $allBouts[] = [
                        'mat' => $mat,
                        'section' => $section,
                        'bouts' => $formattedBouts,
                        'date' => $bouts[0]->date ?? null
                    ];
                }
            }
        }
        
        // 如果沒有找到任何比賽，返回空結果
        if (empty($allBouts)) {
            return response()->json(['message' => 'No bouts found'], 404);
        }
        
        // 生成PDF（使用分頁功能）
        $service = new ProgramScheduleService();
        return $service->allSchedulesPdf($allBouts);
    }

    /**
     * 生成所有賽程的PDF
     */
    protected function generateAllSchedulesPdf($allBouts)
    {
        $this->gameSheet = new ProgramScheduleService();
        
        // 設置標題和logo（根據需要調整）
        // $this->gameSheet->setTitles('比賽總賽程表', 'All Competition Schedule');
        // $this->gameSheet->setLogos('images/jua_logo.png', null);
        
        // 生成PDF
        return $this->gameSheet->allSchedulesPdf($allBouts);
    }

    public function schedule($bouts, $mat = 1)
    {
        if ($bouts == null) {
            $records = [
                [
                    'sequence' => 'A11',
                    'category' => 'CAT A',
                    'weight' => '56kg-',
                    'round' => 'Round 5',
                    'event_volunteerId' => 'ev001',
                    'event_title' => 'Event Title',
                    'rollno' => 'R123',
                    'event_date' => '2024-01-31',
                    'event_time' => '18:30',
                    'event_limit' => '3',
                    'time' => '04:00',
                    'white_player' => 'White player 1',
                    'white_team' => 'White team 1',
                    'blue_player' => 'Blue player 1',
                    'blue_team' => 'Blue team 1',
                ],
                [
                    'sequence' => 'A12',
                    'category' => 'CAT B',
                    'weight' => '66kg-',
                    'round' => 'Round 5',
                    'event_volunteerId' => 'ev001',
                    'event_title' => 'Event Title',
                    'rollno' => 'R123',
                    'event_date' => '2024-01-31',
                    'event_time' => '18:30',
                    'event_limit' => '3',
                    'time' => '03:00',
                    'white_player' => 'White player 2',
                    'white_team' => 'White team 2',
                    'blue_player' => 'Blue player 2',
                    'blue_team' => 'Blue team 2',
                ]

            ];
        } else {
            $records = $bouts;
        }
        // $weight="-66Kg";
        // $category="-Cadet";
        $this->gameSheet->pdf($records, 'MAT' . $mat, '2024.12.31');
    }

    private function smartTruncate($name, $maxLength = 14)
    {
        if (mb_strlen($name) <= $maxLength) {
            return $name;
        }
        
        // 葡文名字通常格式：名 姓
        $parts = explode(' ', $name);
        
        if (count($parts) >= 2) {
            // 先嘗試：前面的部分都只保留首字母，最後一個部分保持完整
            $shortName = '';
            for ($i = 0; $i < count($parts) - 1; $i++) {
                if (!empty($parts[$i])) {
                    $shortName .= mb_substr($parts[$i], 0, 1) . '.';
                }
            }
            
            // 加上完整的姓氏
            $shortName .= ' ' . end($parts);
            
            if (mb_strlen($shortName) <= $maxLength) {
                return $shortName;
            }
            
            // 如果還是太長，使用最簡格式：第一個名字的首字母 + 完整姓氏
            $firstName = mb_substr($parts[0], 0, 1) . '.';
            $lastName = end($parts);
            $simplestName = $firstName . ' ' . $lastName;
            
            if (mb_strlen($simplestName) <= $maxLength) {
                return $simplestName;
            }
        }
        
        // 如果還是太長，直接截斷
        return mb_substr($name, 0, $maxLength - 3) . '...';
    }
}
