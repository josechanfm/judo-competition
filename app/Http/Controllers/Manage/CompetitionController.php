<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\Competition;
use App\Models\CompetitionReferee;
use App\Models\CompetitionCategory;
use App\Models\CompetitionType;
use App\Models\Config;
use App\Models\GameType;
use App\Models\Country;
use App\Models\GameCategory;
use App\Models\Program;
use App\Services\CompetitionExportService;
use App\Services\CompetitionImportService;
use App\Services\Printer\CompetitionResultService;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Manage/Competitions/Index', [
            'countries' => Country::all(),
            'gameTypes' => GameType::all(),
            'competitions' => Competition::with('competition_type')->get(),
            'languages' => Config::item('languages'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('Manage/Competitions/Create', [
            'countries' => Country::all(),
            'gameTypes' => GameType::with('categories')->get(),
            'languages' => Config::item('languages'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_type_id' => 'required',
            'name' => 'required',
            'country' => '',
            // TODO: add filtering
            'scale' => '',
            'system' => '',
            'small_system' => '',
            'competition_type' => '',
            'gender' => '',
            'seeding' => '',
            'name_secondary' => '',
            'type' => '',
            'categories' => '',
            'date_start' => 'required',
            'date_end' => 'required',
            'mat_number' => 'required',
            'section_number' => 'required',
            'days' => 'required',
            'remark' => '',
            'competition.name' => 'required_if:competition,true',
            'competition.language' => 'required_if:competition,true',
            'competition.competition_type.is_language_secondary_enabled' => 'required_if:competition,true|boolean',
            'competition.name_secondary' => 'required_if:competition_is_language_secondary_enabled,true',
            'competition.language_secondary' => 'required_if:competition_is_language_secondary_enabled,true'
        ]);
        $token = Str::random(12);
        $competition_type = $validated['competition_type'];
        // dd(value_column($gameType));
        $competition = Competition::create([
            ...$validated,
            'token' => $token,
            'status' => 0,
            'is_cancelled' => 0,
        ]);
        CompetitionType::create([...$competition_type, 'competition_id' => $competition->id]);
        foreach ($validated['categories'] as $gc) {
            // dd($gc);
            $seq = 1;
            $competitionCategory = CompetitionCategory::create([...$gc, 'competition_id' => $competition->id]);

            if ($competition->gender == 0) {
                $filtered = array_filter($competitionCategory->weights, function ($weight) {
                    return strpos($weight, "FW") !== false;
                });
            } else if ($competition->gender == 1) {
                $filtered = array_filter($competitionCategory->weights, function ($weight) {
                    return strpos($weight, "MW") !== false;
                });
            } else {
                $filtered = $competitionCategory->weights;
            }

            foreach ($filtered as $w) {
                Program::create([
                    'competition_id' => $competition->id,
                    'competition_category_id' => $competitionCategory->id,
                    'date' => $competition->days[0],
                    'mat' => 1,
                    'section' => 1,
                    'weight_code' => $w,
                    'sequence' => $seq,
                    'competition_system' => $competition->system == 'Q' ? 'erm' : ($competition->system == 'F' ? 'full' : 'kos'),
                    'duration' => $competitionCategory->duration,
                    'chart_size' => 0,
                    'status' => 0,
                ]);
                $seq++;
            }
        }
        return redirect()->route('manage.competitions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competition $competition)
    {
        $competition->competition_type;
        // dd(CompetitionCategory::where('competition_id', $competition->id)->get());
        return Inertia::render('Manage/Competitions/Edit', [
            'competition' => $competition,
            'competition_categories' => CompetitionCategory::where('competition_id', $competition->id)->get(),
            'countries' => Country::all(),
            'gameTypes' => GameType::with('categories')->get(),
            'languages' => Config::item('languages'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Competition $competition, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'country' => '',
            // TODO: add filtering
            'scale' => '',
            'system' => '',
            'small_system' => '',
            'gender' => '',
            'seeding' => '',
            'name_secondary' => '',
            'type' => '',
            'date_start' => 'required',
            'date_end' => 'required',
            'mat_number' => 'required',
            'section_number' => 'required',
            'days' => 'required',
            'remark' => '',
            'categories' => '',
            // token 是計分裝置／API 識別賽事的公開鍵，必須唯一
            // （Api\* 全部用 where('token', ...) 查賽事，重複會撈錯賽事）
            'token' => ['required', 'string', 'max:255', Rule::unique('competitions', 'token')->ignore($competition->id)],
            'competition_type.name' => 'required_if:competition_type,true',
            'competition_type.language' => 'required_if:compcompetition_typeetition,true',
            'competition_type.is_language_secondary_enabled' => 'required_if:competition_type,true|boolean',
            'competition_type.name_secondary' => 'required_if:competition_type.is_language_secondary_enabled,true',
            'competition_type.language_secondary' => 'required_if:competition_type.is_language_secondary_enabled,true'
        ]);
        $competition->competition_type->update($validated['competition_type']);
        unset($validated['competition_type']);
        $competition->update($validated);

        return redirect()->route('manage.competitions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition)
    {
        DB::transaction(function () use ($competition) {
            // competition_referee 沒有設定 foreign key，資料庫不會自動清除，需手動刪除
            CompetitionReferee::where('competition_id', $competition->id)->delete();

            // 刪除賽事相關的媒體檔案（標誌、抽籤背景、抽籤封面、證書）
            foreach (['logo', 'draw-background', 'draw-cover', 'certificate'] as $collection) {
                $competition->clearMediaCollection($collection);
            }

            // 其餘關聯資料由資料庫 FK cascade 一併刪除：
            // competition_categories → programs → bouts → bout_results / program_athlete
            // athletes / teams / competition_type
            $competition->delete();
        });

        return redirect()->route('manage.competitions.index');
    }

    /**
     * Cancel the specified competition.
     */
    public function cancel(Competition $competition)
    {
        $competition->update(['is_cancelled' => true]);

        return redirect()->back();
    }

    /**
     * 匯出整場賽事（ZIP：data.json + media/）。
     */
    public function export(Competition $competition, CompetitionExportService $exporter)
    {
        try {
            $file = $exporter->export($competition);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->with('error', $e->getMessage());
        }

        return response()
            ->download($file['path'], $file['filename'])
            ->deleteFileAfterSend(true);
    }

    /**
     * 匯入賽事備份。一律建立一場新賽事，不改動既有資料。
     */
    public function import(Request $request, CompetitionImportService $importer)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:zip'],
        ]);

        try {
            $competition = $importer->import($request->file('file')->getRealPath());
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage() ?: 'Import failed.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'competition' => [
                'id' => $competition->id,
                'name' => $competition->name,
            ],
        ]);
    }

    public function resultTable(Competition $competition, $blankMedals = false)
    {    
        $programsByCategory = $competition->programs()
            ->with(['athletes.team', 'competitionCategory'])
            ->get()
            ->groupBy('competition_category_id');
    
        if ($programsByCategory->isEmpty()) {
            return response()->json(['error' => '沒有找到任何賽程'], 404);
        }

        $resultService = new CompetitionResultService(); // 假設您有對應的賽果服務
        
        // 可以自定義標題和logo
        $resultService->setTitle(
            $competition->name,
            $competition->name_secondary
        );
        $resultService->setCompetitionLogo($competition);
        
        // 生成按分類的賽果表格 PDF
        $pdf = $resultService->generateAllResultTableByCategory($programsByCategory, $blankMedals);

        // 輸出 PDF
        return response($pdf->Output("{$competition->name}賽果表格.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }
}
