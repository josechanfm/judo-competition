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
            ->where('date', $request->date)
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
                'gender' => $bout->program->convertGender(),
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
        
        // 獲取各項得分數量
        $w_ippon = (int)$request['w_ippon'];
        $b_ippon = (int)$request['b_ippon'];
        $w_wazari = (int)$request['w_wazari'];
        $b_wazari = (int)$request['b_wazari'];
        $w_yuko = (int)$request['w_yuko'];
        $b_yuko = (int)$request['b_yuko'];
        $w_shido = (int)$request['w_penalty']['shido'];
        $b_shido = (int)$request['b_penalty']['shido'];
        $w_hansokumake = $request['w_penalty']['hansokumake'];
        $b_hansokumake = $request['b_penalty']['hansokumake'];
        $w_abstain = $request['w_penalty']['abstain'];
        $b_abstain = $request['b_penalty']['abstain'];
        // 根據 status 判斷勝者顏色

        $status = $this->determineMatchStatus($request);
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
        }else if ($bout->competition_system == 'rrb') {
            $this->setRankForRRB($bout->program);
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

    public function determineMatchStatus($request) {
        // 獲取所有分數
        $w_ippon = (int)$request['w_ippon'];
        $b_ippon = (int)$request['b_ippon'];
        $w_wazari = (int)$request['w_wazari'];
        $b_wazari = (int)$request['b_wazari'];
        $w_yuko = (int)$request['w_yuko'];
        $b_yuko = (int)$request['b_yuko'];
        $w_shido = (int)$request['w_penalty']['shido'];
        $b_shido = (int)$request['b_penalty']['shido'];
        
        // 特殊狀態優先檢查（退賽、醫療、犯規輸）
        // 白方特殊狀態
        if ($request['w_penalty']['hansokumake']) {
            return 40; // 白方犯規輸，藍方勝利
        }
        if ($request['w_penalty']['abstain']) {
            return 20; // 白方棄權，藍方勝利
        }
        
        // 藍方特殊狀態
        if ($request['b_penalty']['hansokumake']) {
            return 41; // 藍方犯規輸，白方勝利
        }
        if ($request['b_penalty']['abstain']) {
            return 21; // 藍方棄權，白方勝利
        }
        
        // 檢查是否有 IPPON
        if ($w_ippon > 0 && $b_ippon == 0) {
            return 10; // 白方 IPPON 勝利
        }
        if ($b_ippon > 0 && $w_ippon == 0) {
            return 11; // 藍方 IPPON 勝利
        }
        
        // 如果雙方都有 IPPON（理論上不應該發生，但做檢查）
        if ($w_ippon > 0 && $b_ippon > 0) {
            return 12; // 雙方犯規輸？
        }
        
        // 計算 WAZARI 分數（2個 WAZARI = IPPON 效果）
        $w_wazari_score = $w_wazari;
        $b_wazari_score = $b_wazari;
        
        // 檢查是否有 WAZARI 優勢
        if ($w_wazari_score > $b_wazari_score) {
            return 10; // 白方 WAZARI 優勢勝利
        }
        if ($b_wazari_score > $w_wazari_score) {
            return 11; // 藍方 WAZARI 優勢勝利
        }
        
        // 檢查 SHIDO 犯規次數（4次直接犯規輸）
        if ($w_shido >= 4) {
            return 40; // 白方累積4次犯規，犯規輸
        }
        if ($b_shido >= 4) {
            return 41; // 藍方累積4次犯規，犯規輸
        }
        
        // 檢查 SHIDO 優勢（3次 vs 對方較少）
        if ($w_shido >= 3 && $b_shido < 3) {
            return 10; // 白方因對方3次犯規而勝利
        }
        if ($b_shido >= 3 && $w_shido < 3) {
            return 11; // 藍方因對方3次犯規而勝利
        }
        
        // 如果雙方條件相同，可以比較 YUKO 或其他分數
        if ($w_yuko > $b_yuko) {
            return 10;
        }
        if ($b_yuko > $w_yuko) {
            return 11;
        }
        
        // 如果完全平手，返回 null 或某個值表示比賽進行中
        return null; // 比賽進行中或平手
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
}
