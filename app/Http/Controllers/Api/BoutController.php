<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bout;
use App\Models\Competition;
use App\Models\BoutResult;
use App\Models\ProgramAthlete;
use Illuminate\Http\Request;

class BoutController extends Controller
{
    //
    public function getCompetitionBout($token, Request $request)
    {
        $competition = Competition::where('token', $token)->first();

        if (!$competition) {
            return response()->json([
                'error' => 'Competition not found'
            ], 404);
        }

        $bout = $competition->bouts()
            ->where('mat', $request->mat)
            ->where('section', $request->section)
            ->where('queue', $request->queue)
            ->where('status' ,0)
            ->first();

        if (!$bout) {
            return response()->json([
                'error' => 'Bout not found'
            ], 404);
        }

        // 重新整理數據
        $formattedBout = [
            'firstFighter' => [
                'id' => $bout->white,
                'name' => $bout->white_player->name ?? '待定',
                'name_secondary' => $bout->white_player->name_secondary ?? null,
                'team' => $bout->white_player->team->name ?? '待定',
                'score' => [
                    'ippon' => 0,
                    'wazaari' => 0,
                    'yuko' => 0
                ],
                'penalty' => [
                    'shido' => 0,
                    'hansokumake' => false,
                    'reason' => ''
                ],
                'isInOsaekomi' => false,
                'osaekomiTime' => 0
            ],
            'secondFighter' => [
                'id' => $bout->blue,
                'name' => $bout->blue_player->name ?? '待定',
                'name_secondary' => $bout->blue_player->name_secondary ?? null,
                'team' => $bout->blue_player->team->name ?? '待定',
                'score' => [
                    'ippon' => 0,
                    'wazaari' => 0,
                    'yuko' => 0
                ],
                'penalty' => [
                    'shido' => 0,
                    'hansokumake' => false,
                    'reason' => ''
                ],
                'isInOsaekomi' => false,
                'osaekomiTime' => 0
            ],
            'bout_info' => [
                'id' => $bout->id,
                'name' => $competition->name,
                'queue' => $bout->queue,
                'mat' => $bout->mat,
                'weight' => $bout->program ? $bout->program->convertWeight() : null,
                'category' => $bout->program->competitionCategory->name ?? null,
                'gender' => $bout->program->converGender(),
                'bout_name' => $bout->bout_name
            ],
            'time' => $bout->duration / 60 ,
        ];

        return response()->json([
            'bout' => $formattedBout,
        ]);
    }

    public function postBoutResult($token, Bout $bout, Request $request)
    {
        // 1. 驗證賽事 token
        $competition = Competition::where('token', $token)->first();
        
        if (!$competition) {
            return response()->json([
                'success' => false,
                'message' => '無效的賽事 token'
            ], 404);
        }
        
        // 2. 根據得分規則計算分數
        $w_score = 0;
        $b_score = 0;
        
        // 計算白方得分
        if ($request['w_ippon'] > 0) {
            $w_score += 10; // ippon 直接得10分
        } else {
            $w_score += min($request['w_wazari'] * 7, 10); // wazari 每個7分，上限10分
            $w_score += $request['w_yuko'] * 5; // yuko 每個5分
        }
        
        // 計算藍方得分
        if ($request['b_ippon'] > 0) {
            $b_score += 10; // ippon 直接得10分
        } else {
            $b_score += min($request['b_wazari'] * 7, 10); // wazari 每個7分，上限10分
            $b_score += $request['b_yuko'] * 5; // yuko 每個5分
        }
        
        // 3. 根據 shido 計算對手得分
        $w_score += min($request['b_shido'] * 3, 10); // 對手每個 shido 得3分，上限10分
        $b_score += min($request['w_shido'] * 3, 10); // 對手每個 shido 得3分，上限10分
        
        // 4. 確定勝者並重置敗者分數為0
        $winner = $request['winner']; // 假設請求中包含 winner 欄位，值為 'white' 或 'blue'
        
        if ($winner === 'white') {
            $b_score = 0; // 藍方敗者分數為0
        } elseif ($winner === 'blue') {
            $w_score = 0; // 白方敗者分數為0
        }
        
        // 5. 確保分數不超過10分
        $w_score = min($w_score, 10);
        $b_score = min($b_score, 10);

        $bout->update(['winner'=>$winner,'status'=>1]);
        // 6. 創建或更新 bout_result
        $boutResult = BoutResult::updateOrCreate(
            ['bout_id' => $bout->id],
            [
                'status' => 1,
                'w_ippon' => $request['w_ippon'],
                'w_wazari' => $request['w_wazari'],
                'w_yuko' => $request['w_yuko'],
                'w_shido' => $request['w_shido'],
                'b_ippon' => $request['b_ippon'],
                'b_wazari' => $request['b_wazari'],
                'b_yuko' => $request['b_yuko'],
                'b_shido' => $request['b_shido'],
                'w_score' => $w_score, // 使用計算後的分數
                'b_score' => $b_score, // 使用計算後的分數
                'winner' => $winner, // 儲存勝者資訊
                'time' => $request['time'],
                'device_uuid' => $request['device_uuid'] ?? null,
                'actions' => $request['actions'] ?? null,
            ]
        );

        if ($bout->winner_rise_to != 0 && $winner) {
            $this->updateNextBoutFighter($bout, $winner);
        }


        if ($bout->competition_system === 'kos' && in_array($bout->turn, [1, 2])) {

            $winnerFighterId = $winner === $bout->white ? $bout->white : $bout->blue;
            $loserFighterId = $winner === $bout->white ? $bout->blue : $bout->white;

            // 確保有選手ID
            if (!$winnerFighterId || !$loserFighterId) {
                return;
            }

            // 查找對應的 program_athlete 記錄
            $winnerProgramAthlete = ProgramAthlete::where('id', $winnerFighterId)->first();
            
            $loserProgramAthlete = ProgramAthlete::where('id', $loserFighterId)->first();
            
            if (!$winnerProgramAthlete || !$loserProgramAthlete) {
                return;
            }

            // 根據 turn 設置排名
            if ($bout->turn == 2) {
                // 季軍賽：負方為第3名
                $loserProgramAthlete->update(['rank' => 3]);
                
                \Log::info('KOS 季軍排名設置', [
                    'bout_id' => $bout->id,
                    'turn' => $bout->turn,
                    'loser_athlete_id' => $loserFighterId,
                    'rank' => 3
                ]);
                
            } elseif ($bout->turn == 1) {
                // 冠軍賽：勝方為第1名，負方為第2名
                $winnerProgramAthlete->update(['rank' => 1]);
                $loserProgramAthlete->update(['rank' => 2]);
                
                \Log::info('KOS 冠亞軍排名設置', [
                    'bout_id' => $bout->id,
                    'turn' => $bout->turn,
                    'winner_athlete_id' => $winnerFighterId,
                    'winner_rank' => 1,
                    'loser_athlete_id' => $loserFighterId,
                    'loser_rank' => 2
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => '比賽結果保存成功',
            'data' => [
                'bout_result' => $boutResult,
                'bout' => $bout
            ]
        ]);
    }
    public function updateNextBoutFighter(Bout $currentBout, string $winner)
    {
        // 獲取當前比賽的勝者對應的選手ID
        $winnerFighterId = $winner === $currentBout->white ? $currentBout->white : $currentBout->blue;

        if (!$winnerFighterId) {
            return; // 如果沒有選手ID，則不處理
        }
        
        // 查找下一場比賽（相同 program_id，且 in_program_sequence 等於 winner_rise_to）
        $nextBout = Bout::where('program_id', $currentBout->program_id)
            ->where('in_program_sequence', $currentBout->winner_rise_to)
            ->first();
        
        if (!$nextBout) {
            return; // 如果找不到下一場比賽，則不處理
        }
        
        // 檢查下一場比賽的 white_rise_from 或 blue_rise_from 是否匹配當前比賽的 in_program_sequence
        $updateData = [];
        
        if ($nextBout->white_rise_from == $currentBout->in_program_sequence) {
            $updateData['white'] = $winnerFighterId;
        } elseif ($nextBout->blue_rise_from == $currentBout->in_program_sequence) {
            $updateData['blue'] = $winnerFighterId;
        }

        // 如果有需要更新的資料，則更新下一場比賽
        if (!empty($updateData)) {
            $nextBout->update($updateData);
            
            // 可選：記錄日誌以便調試
            \Log::info('勝者晉級更新', [
                'current_bout_id' => $currentBout->id,
                'current_sequence' => $currentBout->in_program_sequence,
                'winner' => $winner,
                'winner_fighter_id' => $winnerFighterId,
                'next_bout_id' => $nextBout->id,
                'next_sequence' => $nextBout->in_program_sequence,
                'updated_fields' => array_keys($updateData)
            ]);
        }
    }

    public function updateKOSRanking(Bout $bout, string $winner)
    {
        $winnerFighterId = $winner === $bout->white ? $bout->white : $bout->blue;
        $loserFighterId = $winner === $bout->white ? $bout->blue : $bout->white;
        
        // 確保有選手ID
        if (!$winnerFighterId || !$loserFighterId) {
            return;
        }
        
        // 查找對應的 program_athlete 記錄
        $winnerProgramAthlete = ProgramAthlete::where('program_id', $bout->program_id)
            ->where('athlete_id', $winnerFighterId)
            ->first();
        
        $loserProgramAthlete = ProgramAthlete::where('program_id', $bout->program_id)
            ->where('athlete_id', $loserFighterId)
            ->first();
        
        if (!$winnerProgramAthlete || !$loserProgramAthlete) {
            return;
        }
        
        // 根據 turn 設置排名
        if ($bout->turn == 2) {
            // 季軍賽：負方為第3名
            $loserProgramAthlete->update(['rank' => 3]);
            
            \Log::info('KOS 季軍排名設置', [
                'bout_id' => $bout->id,
                'turn' => $bout->turn,
                'loser_athlete_id' => $loserFighterId,
                'rank' => 3
            ]);
            
        } elseif ($bout->turn == 1) {
            // 冠軍賽：勝方為第1名，負方為第2名
            $winnerProgramAthlete->update(['rank' => 1]);
            $loserProgramAthlete->update(['rank' => 2]);
            
            \Log::info('KOS 冠亞軍排名設置', [
                'bout_id' => $bout->id,
                'turn' => $bout->turn,
                'winner_athlete_id' => $winnerFighterId,
                'winner_rank' => 1,
                'loser_athlete_id' => $loserFighterId,
                'loser_rank' => 2
            ]);
        }
    }
}
