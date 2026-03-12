<?php

namespace App\Http\Controllers\Manage;
use App\Http\Controllers\Controller;
use App\Models\Bout;
use App\Models\BoutResult;
use App\Models\Competition;
use App\Models\Program;
use App\Models\ProgramAthlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoutController extends Controller
{
    //
    public function createOrUpdateResult(Competition $competition, Bout $bout, Request $request)
    {
        if (!$competition) {
            return response()->json([
                'success' => false,
                'message' => '無效的賽事 token'
            ], 404);
        }
        // 獲取各項得分數量
        $w_ippon = (int)$request['w_ippon'];
        $b_ippon = (int)$request['b_ippon'];
        $w_wazari = (int)$request['w_wazari'];
        $b_wazari = (int)$request['b_wazari'];
        $w_yuko = (int)$request['w_yuko'];
        $b_yuko = (int)$request['b_yuko'];
        $w_shido = (int)$request['w_shido'];
        $b_shido = (int)$request['b_shido'];
        
        // 根據 status 判斷勝者顏色
        $status = (int)$request['status'];
        $winnerColor = null;
        $winnerId = null;
        
        // 判斷勝者顏色
        if (in_array($status, [
            BoutResult::STATUS_WHITE_WIN,
            BoutResult::STATUS_WHITE_ABSTAIN,
            BoutResult::STATUS_WHITE_MEDICAL,
            BoutResult::STATUS_WHITE_HANSOKUMAKE
        ])) {
            $winnerColor = 'white';
            $winnerId = $bout->white; // 白方選手的 ID
        } elseif (in_array($status, [
            BoutResult::STATUS_BLUE_WIN,
            BoutResult::STATUS_BLUE_ABSTAIN,
            BoutResult::STATUS_BLUE_MEDICAL,
            BoutResult::STATUS_BLUE_HANSOKUMAKE
        ])) {
            $winnerColor = 'blue';
            $winnerId = $bout->blue; // 藍方選手的 ID
        }
        // 初始化分數
        $w_score = 0;
        $b_score = 0;
        
        // 根據不同的 status 計算勝方分數
        if (in_array($status, [
            BoutResult::STATUS_WHITE_ABSTAIN,
            BoutResult::STATUS_WHITE_MEDICAL,
            BoutResult::STATUS_WHITE_HANSOKUMAKE,
            BoutResult::STATUS_BLUE_ABSTAIN,
            BoutResult::STATUS_BLUE_MEDICAL,
            BoutResult::STATUS_BLUE_HANSOKUMAKE
        ])) {
            // 退賽、醫療、犯規輸的情況：勝方直接得10分
            if ($winnerColor === 'white') {
                $w_score = 10;
            } elseif ($winnerColor === 'blue') {
                $b_score = 10;
            }
        } elseif (in_array($status, [BoutResult::STATUS_WHITE_WIN, BoutResult::STATUS_BLUE_WIN])) {
            // 普通勝利的情況：根據技術得分判斷
            
            // 先比較 ippon
            if ($w_ippon > $b_ippon) {
                // 白方 ippon 多於藍方
                $w_score = 10;
            } elseif ($b_ippon > $w_ippon) {
                // 藍方 ippon 多於白方
                $b_score = 10;
            } else {
                // ippon 相同，比較 wazari
                if ($w_wazari > $b_wazari) {
                    // 白方 wazari 多於藍方
                    $w_score = ($w_wazari >= 2) ? 10 : 7;
                } elseif ($b_wazari > $w_wazari) {
                    // 藍方 wazari 多於白方
                    $b_score = ($b_wazari >= 2) ? 10 : 7;
                } else {
                    // ippon 和 wazari 都相同，比較 yuko
                    if ($w_yuko > $b_yuko) {
                        $w_score = 5; // 白方 yuko 較多得5分
                    } elseif ($b_yuko > $w_yuko) {
                        $b_score = 5; // 藍方 yuko 較多得5分
                    }
                    // 如果 yuko 也相同，理論上不應該發生，因為有 winner
                }
            }
        }
        
        // 根據 shido 計算對手得分（只有當對手有3個shido時才得10分）
        if ($b_shido >= 3) {
            $w_score += 10; // 藍方有3個以上shido，白方得10分
        }
        
        if ($w_shido >= 3) {
            $b_score += 10; // 白方有3個以上shido，藍方得10分
        }
        
        // 敗者分數為0
        if ($winnerColor === 'white') {
            $b_score = 0;
        } elseif ($winnerColor === 'blue') {
            $w_score = 0;
        }
        
        // 確保分數不超過10分
        $w_score = min($w_score, 10);
        $b_score = min($b_score, 10);
        
        // 更新比賽狀態為已完成，winner 存入選手ID
        $bout->update([
            'winner' => $winnerId, // 這裡存入選手的 ID，而不是 'white'/'blue'
            'status' => 1  // 比賽完成狀態
        ]);
        
        // 創建或更新 bout_result
        $boutResult = BoutResult::updateOrCreate(
            ['bout_id' => $bout->id],
            [
                'status' => $status, // 儲存原始的結果狀態 (10,11,20,21,30,31,40,41)
                'w_ippon' => $w_ippon,
                'w_wazari' => $w_wazari,
                'w_yuko' => $w_yuko,
                'w_shido' => $w_shido,
                'b_ippon' => $b_ippon,
                'b_wazari' => $b_wazari,
                'b_yuko' => $b_yuko,
                'b_shido' => $b_shido,
                'w_score' => $w_score,
                'b_score' => $b_score,
                'time' => $request['time'],
            ]
        );
        
        // 更新下一輪比賽的選手（這裡需要傳入選手ID）
        if ($bout->winner_rise_to != 0 && $winnerId) {
            $this->updateNextBoutFighter($bout, $winnerId);
        }
        // KOS 賽制排名處理
        if ($bout->competition_system === 'kos' && in_array($bout->turn, [1, 2])) {
            
            $winnerFighterId = $winnerId; // 直接使用勝者ID
            $loserFighterId = $winnerColor === 'white' ? $bout->blue : $bout->white;
            
            // 確保有選手ID
            if (!$winnerFighterId || !$loserFighterId) {
                return response()->json([
                    'success' => true,
                    'message' => '比賽結果保存成功（但排名更新失敗：找不到選手）',
                    'data' => [
                        'bout_result' => $boutResult,
                        'bout' => $bout
                    ]
                ]);
            }
            
            // 查找對應的 program_athlete 記錄
            $winnerProgramAthlete = ProgramAthlete::where('id', $winnerFighterId)->first();
            $loserProgramAthlete = ProgramAthlete::where('id', $loserFighterId)->first();
            
            if (!$winnerProgramAthlete || !$loserProgramAthlete) {
                return response()->json([
                    'success' => true,
                    'message' => '比賽結果保存成功（但排名更新失敗：找不到運動員記錄）',
                    'data' => [
                        'bout_result' => $boutResult,
                        'bout' => $bout
                    ]
                ]);
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
        }else if($bout->competition_system == 'rrb'){
            $this->setRankForRRB($bout->program);
        }
        $this->checkProgramCompletion($bout->program);

        return redirect()->back();
    }

    public function updateQueue(Request $request, Competition $competition)
    {
        $updates = $request->input('updates', []);
        
        DB::transaction(function () use ($updates) {
            foreach ($updates as $update) {
                Bout::where('id', $update['id'])
                    ->update(['queue' => $update['queue']]);
            }
        });
        
        return back()->with('success', '場次順序已更新');
    }
    public function setRankForRRB($program)
    {
        // 獲取所有選手並計算分數
        $athletes = $program->programAthletes;
        $athletes->each(function (ProgramAthlete $athlete) {
            $athlete->collectScore();
        });
        // 按分數降序排序
        $sortedAthletes = $athletes->sortByDesc('score')->values();
        $totalCount = $sortedAthletes->count();
        
        // 為每個選手計算排名
        foreach ($sortedAthletes as $index => $currentAthlete) {
            $rank = $totalCount; // 初始排名為最後一名
            
            // 與其他所有選手比較
            for ($i = 0; $i < $totalCount; $i++) {
                if ($i === $index) continue; // 跳過自己
                
                $otherAthlete = $sortedAthletes[$i];
                
                // 判斷是否應該提高排名（是否贏過對方）
                $shouldIncreaseRank = false;
                
                if ($currentAthlete->score > $otherAthlete->score) {
                    $shouldIncreaseRank = true;
                } 
                else if ($currentAthlete->score == $otherAthlete->score) {
                    $bout = Bout::where(function($query) use ($currentAthlete, $otherAthlete) {
                    $query->where('white', $currentAthlete->id)
                        ->where('blue', $otherAthlete->id);
                    })->orWhere(function($query) use ($currentAthlete, $otherAthlete) {
                        $query->where('white', $otherAthlete->id)
                            ->where('blue', $currentAthlete->id);
                    })->first();
                    if ($bout) {
                        $shouldIncreaseRank = ($bout->winner == $currentAthlete->id);
                    }
                }
                
                if ($shouldIncreaseRank) {
                    $rank--; // 排名提前
                }
            }
            
            // 5名選手的特殊排名處理
            if ($totalCount == 5) {
                $specialRankMap = [4 => 3]; // 第4名變成第3名
                $rank = $specialRankMap[$rank] ?? $rank;
            }
            
            // 設定排名
            $currentAthlete->setRank($rank);
        }
    }
    public function updateNextBoutFighter(Bout $currentBout, string $winner)
    {
        // 獲取當前比賽的勝者對應的選手ID
        $winnerFighterId = $winner;

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
    private function checkProgramCompletion($program)
    {
        // 獲取該 program 下的所有比賽
        $bouts = Bout::where('program_id', $program->id)->get();
        
        // 檢查是否所有比賽的 status 都等於 1（已完成）
        $allCompleted = $bouts->every(function ($bout) {
            return $bout->status == 1;
        });
        
        // 如果所有比賽都已完成，將 program 的 status 設為 4
        if ($allCompleted && $bouts->count() > 0) {
            Program::where('id', $program->id)->update(['status' => 4]);
            
            \Log::info('Program 所有比賽已完成，狀態更新為 4', [
                'program_id' => $program->id,
                'bouts_count' => $bouts->count()
            ]);
        }
    }
}
