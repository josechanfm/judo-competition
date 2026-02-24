<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\ProgramAthlete;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    //
    public function getCompetition($token) {
        $competition = Competition::where('token', $token)->first();

        return response()->json([
            'competition' => $competition,
        ]);
    }

    public function getCompetitionBouts($token, Request $request)
    {
        $competition = Competition::where('token', $token)->first();

        if (!$competition) {
            return response()->json([
                'error' => 'Competition not found'
            ], 404);
        }

        $bouts = $competition->bouts()
            ->where('mat', $request->mat)
            ->where('section', $request->section)
            ->where('queue', '!=', 0)
            ->where('status', 0)
            ->orderBy('queue')
            ->get();

        // 重新整理數據
        $formattedBouts = $bouts->map(function ($bout) {
            return [
                'queue' => $bout->queue,
                'white_player' => [
                    'name_display' => ($bout->white_player->name ?? null) . ($bout->white_player->name_secondary ?? null),
                    'team' => $bout->white_player->team->name ?? null,
                ],
                'blue_player' => [
                    'name_display' => ($bout->blue_player->name ?? null) . ($bout->blue_player->name_secondary ?? null),
                    'team' => $bout->blue_player->team->name ?? null,
                ],
                'category' => $bout->program->competitionCategory->name ?? null,
                'weight' => $bout->program ? $bout->program->convertWeight() : null,
                'program_id' => $bout->program_id,
            ];
        });

        return response()->json([
            'bouts' => $formattedBouts,
        ]);
    }
    public function fetchCompetitionData()
    {   
        $competition = Competition::where('token','FI5DymF3I4OK')->first();
        $version = time();
        
        $categories = $competition->categories->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name
            ];
        });

        $weights = $competition->categories->flatMap(function($category) {
            return $category->programs->map(function($program) {
                return [
                    'id' => $program->id,
                    'category_id' => $program->competition_category_id,
                    'name' => $program->convertGender() . $program->convertWeight(),
                    'max' => $program->maxWeight(),
                    'min' => $program->minWeight(),
                ];
            });
        });
        $athletes = $competition->programAthletes()->get()->map(function ($athlete){
            return [
                'id' => $athlete->id,
                'weight_id' => $athlete->program_id,
                'name'=> $athlete->athlete->name . $athlete->athlete->name_secondary,
                'actual' => null,
                'signature' => null,
            ];
        });

        return response()->json([
            'version' => $version,
            'categories' => $categories,
            'weights' => $weights ,
            'athletes' => $athletes, 
        ]);
    }
public function postCompetitionData(Request $request)
{
    $validated = $request->validate([
        'version' => 'required|integer',
        'athletes' => 'required|array',
        'categories' => 'required|array',
        'weights' => 'required|array',
        'competition_token' => 'required|string',
    ]);
    
    // 查找比賽 - 使用 first() 而不是 firstOrFail()
    $competition = Competition::where('token', $validated['competition_token'])->first();
    
    // 檢查比賽是否存在
    if (!$competition) {
        return response()->json([
            'success' => false,
            'message' => '比賽token錯誤',
            'error_code' => 'COMPETITION_NOT_FOUND'
        ], 404); // 使用 404 Not Found 狀態碼
    }
    
    $updatedCount = 0;
    $errors = [];
       
    // 處理每個運動員的數據
    foreach ($validated['athletes'] as $athleteData) {
        try {
            // 查找運動員
            $programAthlete = ProgramAthlete::find($athleteData['id']);
            
            if (!$programAthlete) {
                $errors[] = "運動員 ID {$athleteData['id']} 不存在";
                continue;
            }
            
            // 驗證運動員是否屬於該比賽
            if ($programAthlete->program->category->competition_id !== $competition->id) {
                $errors[] = "運動員 ID {$athleteData['id']} 不屬於此比賽";
                continue;
            }
            
            // 更新數據
            $updateData = [];
            
            if (isset($athleteData['actual'])) {
                $updateData['weight'] = $athleteData['actual'];
                if(!isset($athleteData['signature'])){
                    $updateData['is_weight_passed'] = 1;
                }
            }
            
            if (isset($athleteData['signature'])) {
                $updateData['signature'] = $athleteData['signature'];
                $updateData['is_weight_passed'] = 0;
            }

            if (!empty($updateData)) {
                $programAthlete->update($updateData);
                $updatedCount++;
            }
            
        } catch (\Exception $e) {
            $errors[] = "處理運動員 ID {$athleteData['id']} 時出錯: " . $e->getMessage();
        }
    }

    // 返回響應
    $response = [
        'success' => true,
        'message' => "成功更新 {$updatedCount} 條記錄",
        'version' => time(), // 返回新的版本號
    ];
    
    // 如果有錯誤，添加到響應中
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['success'] = false; // 如果有錯誤，標記為失敗
        $response['message'] = "部分記錄更新失敗";
    }
    
    return response()->json($response);
}
}
