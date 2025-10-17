<?php

namespace App\Http\Controllers\Manage\Printer;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Services\Printer\TournamentQuarterService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TournamentKnockoutController extends Controller
{
    protected TournamentQuarterService $gameSheet;
    protected ?array $dummyData = null;
    
    // 常數定義
    protected const POOL_LABELS = [
        ['name' => '分組 A'],
        ['name' => '分組 B'],
        ['name' => '分組 C'],
        ['name' => '分組 D']
    ];
    
    protected const DEFAULT_WINNER_LIST = [
        ['award' => 'Gold', 'name' => ''],
        ['award' => 'Silver', 'name' => ''],
        ['award' => 'Brown', 'name' => ''],
        ['award' => 'Brown', 'name' => ''],
    ];

    public function printPdf(Request $request)
    {
        try {
            // 載入設定檔
            $settings = $this->loadSettings('game_tournament_knockout.json');
            $this->dummyData = $this->loadSettings('game_tournament_knockout_dataset.json');
            
            // 取得節目資料
            $program = $this->getProgram($request);
            $size = $this->getChartSize($request, $program);
            
            // 初始化遊戲表格服務
            $this->initializeGameSheet($settings, $request);
            
            // 取得玩家資料
            $players = $this->getPlayers($program, $size);
            
            // 根據尺寸生成對應的PDF
            $this->generatePdfBySize($size, $players);
            
        } catch (\Exception $e) {
            Log::error('PDF生成失敗: ' . $e->getMessage());
            return response()->json(['error' => 'PDF生成失敗'], 500);
        }
    }

    /**
     * 載入設定檔案
     */
    private function loadSettings(string $filename): array
    {
        $filePath = storage_path("setting/{$filename}");
        
        if (!File::exists($filePath)) {
            throw new \Exception("設定檔案不存在: {$filename}");
        }
        
        return File::json($filePath);
    }

    /**
     * 取得節目資料
     */
    private function getProgram(Request $request): ?Program
    {
        if (!$request->has('program')) {
            return null;
        }
        
        return Program::find($request->program);
    }

    /**
     * 取得圖表尺寸
     */
    private function getChartSize(Request $request, ?Program $program): int
    {
        return $program->chart_size ?? $request->size ?? 32;
    }

    /**
     * 初始化遊戲表格服務
     */
    private function initializeGameSheet(array $settings, Request $request): void
    {
        $this->gameSheet = new TournamentQuarterService($settings);
        
        // 設定字型
        $this->gameSheet->setFonts('times', 'cid0ct', 'times');
        
        // 設定分組標籤
        $this->gameSheet->setPoolLabel(self::POOL_LABELS);
        
        // 設定勝者線顯示
        if ($request->winner_line) {
            $this->gameSheet->setWinnerLine(true);
        }
    }

    /**
     * 從節目取得玩家資料
     */
    private function getPlayers(?Program $program, int $size): ?array
    {
        if (!$program || !$program->bouts) {
            return null;
        }
        
        return $program->bouts
            ->take($size / 2)
            ->map(function ($bout) {
                return [
                    'white' => ['name_display' => $bout->white_player->name_display ?? ''],
                    'blue' => ['name_display' => $bout->blue_player->name_display ?? '']
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * 根據尺寸生成PDF
     */
    private function generatePdfBySize(int $size, ?array $players): void
    {
        $methodName = 'generate' . $size . 'PlayersPdf';
        
        if (method_exists($this, $methodName)) {
            $this->$methodName($players);
            return;
        }
        
        // 預設處理32位玩家
        $this->generate32PlayersPdf($players);
    }

    /**
     * 載入虛擬資料
     */
    private function loadDummyData(int $size): array
    {
        if (!isset($this->dummyData[$size])) {
            throw new \Exception("不支援的玩家尺寸: {$size}");
        }
        
        return $this->dummyData[$size];
    }

    /**
     * 生成PDF的通用方法
     */
    private function generatePdf(int $size, ?array $players, array $defaultData = null): void
    {
        $data = $players ? [
            'players' => $players,
            'winners' => null,
            'sequences' => $this->generateSequences($size),
            'winnerList' => self::DEFAULT_WINNER_LIST
        ] : $this->loadDummyData($size);
        
        $this->gameSheet->pdf(
            $data['players'] ?? [],
            $data['winners'] ?? [],
            $data['sequences'] ?? [],
            $data['winnerList'] ?? []
        );
    }

    /**
     * 生成序列號
     */
    private function generateSequences(int $size): array
    {
        // 根據尺寸生成對應的序列號
        // 這裡可以根據實際邏輯擴充
        return [[1]]; // 簡化版本，實際應根據尺寸生成
    }

    // 以下為各尺寸的具體實現
    private function generate2PlayersPdf(?array $players): void
    {
        $this->generatePdf(2, $players);
    }

    private function generate4PlayersPdf(?array $players): void
    {
        $this->generatePdf(4, $players);
    }

    private function generate8PlayersPdf(?array $players): void
    {
        $this->generatePdf(8, $players);
    }

    private function generate16PlayersPdf(?array $players): void
    {
        $this->generatePdf(16, $players);
    }

    private function generate32PlayersPdf(?array $players): void
    {
        $this->generatePdf(32, $players);
    }

    private function generate64PlayersPdf(?array $players = null): void
    {
        // 64位玩家的特殊處理
        $players = $players ?? $this->generate64PlayersData();
        
        $winners = [
            array_fill(0, 32, 1), // 第一輪
            array_fill(0, 16, 1), // 第二輪
            array_fill(0, 8, 1),  // 第三輪
            array_fill(0, 4, 1),  // 第四輪
            array_fill(0, 2, 1),  // 第五輪
            [1]                   // 決賽
        ];
        
        $sequences = $this->generate64PlayersSequences();
        
        $this->gameSheet->pdf($players, $winners, $sequences, self::DEFAULT_WINNER_LIST);
    }

    /**
     * 生成64位玩家資料
     */
    private function generate64PlayersData(): array
    {
        $players = [];
        
        for ($i = 1; $i <= 32; $i++) {
            $players[] = [
                'white' => ['name_display' => "White player " . ($i * 2 - 1)],
                'blue' => ['name_display' => "Blue player " . ($i * 2)]
            ];
        }
        
        return $players;
    }

    /**
     * 生成64位玩家序列號
     */
    private function generate64PlayersSequences(): array
    {
        return [
            range(1, 32),   // 第一輪
            range(33, 48),  // 第二輪
            range(49, 56),  // 第三輪
            range(57, 60),  // 第四輪
            range(61, 62),  // 第五輪
            [63]            // 決賽
        ];
    }
}