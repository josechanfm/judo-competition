<?php

namespace App\Http\Controllers\Manage;
use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function previewBracket()
    {
        // 預覽頁面
        $data = $this->getTournamentData();
        return view('tournament.bracket', $data);
    }

    public function generateBracket()
    {
        $data = $this->getTournamentData();
        
        $pdf = Pdf::loadView('tournament.bracket', $data);
        
        // 關鍵設定：強制單頁顯示
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('dpi', 96);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        // dd('aa');
        // 防止分頁的關鍵設定
        $pdf->setOption('defaultFont', 'simhei'); // 使用中文字體
        $pdf->setOption('isRemoteEnabled', true);
        
        return $pdf->download('柔道比賽表.pdf');
    }

    private function getTournamentData()
    {
        return [
            'tournamentName' => '2024 全國柔道錦標賽',
            'weightClass' => '男子 73公斤級',
            'date' => '2024年12月15日',
            'rounds' => [
                [
                    'name' => '十六強賽',
                    'matches' => [
                        ['id' => 1, 'player1' => '張三', 'score1' => '一本', 'player2' => '李四', 'score2' => '有效', 'winner' => 1],
                        ['id' => 2, 'player1' => '王五', 'score1' => '技有', 'player2' => '趙六', 'score2' => '指導', 'winner' => 1],
                        ['id' => 3, 'player1' => '陳七', 'score1' => '綜合勝利', 'player2' => '林八', 'score2' => '', 'winner' => 1],
                        ['id' => 4, 'player1' => '黃九', 'score1' => '棄權', 'player2' => '劉十', 'score2' => '', 'winner' => 2],
                        ['id' => 5, 'player1' => '周十一', 'score1' => '判定', 'player2' => '吳十二', 'score2' => '', 'winner' => 1],
                        ['id' => 6, 'player1' => '鄭十三', 'score1' => '犯規負', 'player2' => '孫十四', 'score2' => '', 'winner' => 2],
                        ['id' => 7, 'player1' => '朱十五', 'score1' => '', 'player2' => '馬十六', 'score2' => '', 'winner' => 1],
                        ['id' => 8, 'player1' => '胡十七', 'score1' => '', 'player2' => '郭十八', 'score2' => '', 'winner' => 2],
                    ]
                ],
                [
                    'name' => '八強賽',
                    'matches' => [
                        ['id' => 9, 'player1' => '張三', 'player2' => '王五', 'winner' => 1],
                        ['id' => 10, 'player1' => '陳七', 'player2' => '劉十', 'winner' => 2],
                        ['id' => 11, 'player1' => '周十一', 'player2' => '孫十四', 'winner' => 1],
                        ['id' => 12, 'player1' => '朱十五', 'player2' => '郭十八', 'winner' => 1],
                    ]
                ],
                [
                    'name' => '四強賽',
                    'matches' => [
                        ['id' => 13, 'player1' => '張三', 'player2' => '劉十', 'winner' => 1],
                        ['id' => 14, 'player1' => '周十一', 'player2' => '朱十五', 'winner' => 1],
                    ]
                ],
                [
                    'name' => '決賽',
                    'matches' => [
                        ['id' => 15, 'player1' => '張三', 'player2' => '周十一', 'winner' => 1],
                    ]
                ]
            ]
        ];
    }
}