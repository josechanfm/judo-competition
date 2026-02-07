<?php

namespace App\Http\Controllers\Manage;

use App\Exports\MedalQuantity;
use App\Exports\ProgramTimeExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Competition;
use App\Models\Program;
use App\Services\BoutGenerationService;
use App\Services\Printer\TournamentQuarterService;
use App\Models\Bout;
use App\Models\Athlete;
use App\Models\ProgramAthlete;
use App\Services\CustomTCPDF;
use App\Services\FontService;
use App\Services\Printer\RoundRobbinOption1Service;
use App\Services\Printer\RoundRobbinOption2Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use PgSql\Lob;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Competition $competition)
    {
        //$competition->categories;
        // $program=Program::find(1);
        // dd($competition->bouts()->get());
        return Inertia::render('Manage/Programs', [
            'programs' => $competition->programs()
                //->with('competitionCategory')
                ->withCount('athletes', 'bouts')
                ->orderBy('date')
                ->orderBy('section')
                ->orderBy('mat')
                ->orderBy('sequence')
                ->get(),
            'athletes' => $competition->athletes,
            'competition' => $competition
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition, Program $program)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'program' => $program,
                'program_athletes' => $program->programAthletes
            ]);
        }
        return Inertia::render('Manage/Program', [
            'program' => $program->load(['programAthletes.athlete', 'programAthletes.athlete.team', 'bouts']),
            'athletes' => $program->athletes,
            'competition' => $competition
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function removeAthlete($programId, $athleteId)
    {
        ProgramAthlete::where('program_id', $programId)->where('athlete_id', $athleteId)->delete();

        return redirect()->back();
    }
    public function joinAthlete($programId, $athleteId)
    {
        ProgramAthlete::insert(['program_id' => $programId, 'athlete_id' => $athleteId]);

        return redirect()->back();
    }
    public function progress(Competition $competition)
    {
        $competition->programAthletes;
        $competition->programsBouts;

        $competition->bouts = $competition->bouts()->where('queue', '!=', 0)->orderBy('queue')->get();
        // dd($competition->bouts);
        return Inertia::render('Manage/ProgramProgress', [
            'competition' => $competition,
        ]);
    }

    public function draw(Competition $competition, Program $program)
    {
        $athletes = $program->draw();

        $program->confirmDraw();

        return response()->json([
            'athletes' => $athletes
        ]);
    }

    public function resetDraw(Competition $competition, Program $program)
    {
        $program->update(['status' => 0]);

        $program->programAthletes()->update(['seat' => 0]);

        $program->bouts()->update(['white' => 0, 'queue' => 1, 'blue' => 0, 'winner' => 0, 'status' => 0]);

        $service = (new BoutGenerationService($competition));
        // dd($service);
        $service->resequence();

        return response()->json([
            'athletes' => $program->programAthletes
        ]);
    }

    public function chartPdf(Competition $competition)
    {
        $program = Program::find(4);
        //$bouts=$competition->bouts->where('section',1)->where('mat',1);
        $bouts = Bout::whereBelongsTo($program)->where('section', 1)->where('mat', 1)->get();
        foreach ($bouts as $i => $bout) {
            //$bouts[$i]->circle=$bout->sequence;
            $bouts[$i]->circle = $bout->in_program_sequence;
        };
        $program->athletes;
        $data = [
            'program' => $program,
            'bouts' => $bouts
        ];
        // return view('chartPdf.tournament'.$program->chart_size,$data);

        $pdf = Pdf::loadView('chartPdf.tournament' . $program->chart_size, $data);
        return $pdf->stream();
        //return $pdf->download('chartPdf.pdf');
    }
    public function lock(Competition $competition)
    {
        // remove previously generated bouts
        // dd($competition);
        $competition->programsBouts;
        
        $competition->bouts()->delete();

        // TODO: generate bouts
        $competition->generateBouts();
        // dd($competition);
        $competition->update(['status' => 2]);

        return redirect()->back();
    }
    public function gen_bouts(Competition $competition)
    {
        //Create bouts records according to the contest system of each program, the in_program_sequence is given, but the sequence column is temporary
        $competition->bouts()->delete();
        $programs = $competition->programs->sortBy('sequence')->sortByDesc('chart_size');
        $data = [];
        $sequence = 1;
        foreach ($programs as $program) {
            $round = $program->chart_size;
            $inProgramSequence = 1;
            while ($round > 0) {
                $roundSize = $round / 2;
                for ($t = 1; $t <= $roundSize; $t++) {
                    $data[] = [
                        'program_id' => $program->id,
                        'sequence' => $sequence++,
                        'in_program_sequence' => $inProgramSequence++,
                        'queue' => 0,
                        'mat' => $program->mat,
                        'section' => $program->section,
                        'competition_system' => $program->competition_system,
                        'round' => $round,
                        'turn' => 0,
                        'white' => 0,
                        'blue' => 0,
                    ];
                }
                //repeatcharge round 1, only for program competition_system "erm" 
                if ($program->competition_system == 'erm') {
                    if ($roundSize == 4) {
                        $data[] = [
                            'program_id' => $program->id,
                            'sequence' => $sequence++,
                            'in_program_sequence' => $inProgramSequence++,
                            'queue' => 0,
                            'mat' => $program->mat,
                            'section' => $program->section,
                            'competition_system' => $program->competition_system,
                            'round' => 7,
                            'turn' => 0,
                            'white' => 0,
                            'blue' => 0,
                        ];
                        $data[] = [
                            'program_id' => $program->id,
                            'sequence' => $sequence++,
                            'in_program_sequence' => $inProgramSequence++,
                            'queue' => 0,
                            'mat' => $program->mat,
                            'section' => $program->section,
                            'competition_system' => $program->competition_system,
                            'round' => 7,
                            'turn' => 0,
                            'white' => 0,
                            'blue' => 0,
                        ];
                    }
                    if ($roundSize == 2 && $program->chart_size > 4) {
                        $data[] = [
                            'program_id' => $program->id,
                            'sequence' => $sequence++,
                            'in_program_sequence' => $inProgramSequence++,
                            'queue' => 0,
                            'mat' => $program->mat,
                            'section' => $program->section,
                            'competition_system' => $program->competition_system,
                            'round' => 3,
                            'turn' => 0,
                            'white' => 0,
                            'blue' => 0,
                        ];
                        $data[] = [
                            'program_id' => $program->id,
                            'sequence' => $sequence++,
                            'in_program_sequence' => $inProgramSequence++,
                            'queue' => 0,
                            'mat' => $program->mat,
                            'section' => $program->section,
                            'competition_system' => $program->competition_system,
                            'round' => 3,
                            'turn' => 0,
                            'white' => 0,
                            'blue' => 0,
                        ];
                    }
                }
                $round = $round / 2;
            }
        }
        //dd($data);
        Bout::upsert(
            $data,
            ['program_id', 'in_program_sequence'],
            ['sequence', 'queue', 'mat', 'section', 'competition_system', 'round', 'turn', 'white', 'blue']
        );

        //Rearrage the sequence number based on section and mat id, the bouts are order by chart_size and sequence of programs
        $matNumber = $competition->mat_number;
        $sectionNumber = $competition->section_number;
        for ($s = 1; $s <= $sectionNumber; $s++) {
            for ($m = 1; $m <= $matNumber; $m++) {
                $i = 1;
                $bouts = $competition->bouts->where('section', $s)->where('mat', $m)->sortByDesc('round');
                foreach ($bouts as $bout) {
                    $bout->sequence = $i++;
                    $bout->save();
                }
            }
        }

        //Assign athletes sequencially to bouts list
        $programs = $competition->programs;
        foreach ($programs as $program) {
            $athletes = $program->athletes;
            $bouts = $program->bouts;
            $a = 0;
            for ($i = 0; $i < $program->chart_size / 2; $i++) {
                if (isset($athletes[$a])) {
                    $bouts[$i]->white = $athletes[$a++]->pivot->athlete_id;
                    $bouts[$i]->save();
                };
                if (isset($athletes[$a])) {
                    $bouts[$i]->blue = $athletes[$a++]->pivot->athlete_id;
                    $bouts[$i]->save();
                }
            }
        };

        //Assign rise from, both whtie_rise_from and blue_rise_from
        //erm
        $riseFromErm = [
            '4' => [
                '3' => [1, 2],
            ],
            '8' => [
                '5' => [-2, -4],
                '6' => [-1, -3],
                '7' => [1, 3],
                '8' => [2, 4],
                '9' => [5, -8],
                '10' => [6, -7],
                '11' => [7, 8]
            ],
            '16' => [
                '9' => [1, 5],
                '10' => [2, 6],
                '11' => [3, 7],
                '12' => [4, 8],
                '13' => [-10, -12],
                '14' => [-9, -11],
                '15' => [9, 11],
                '16' => [10, 12],
                '17' => [13, -16],
                '18' => [14, -15],
                '19' => [15, 16]
            ],
            '32' => [
                '17' => [1, 9],
                '18' => [2, 10],
                '19' => [3, 11],
                '20' => [4, 12],
                '21' => [5, 13],
                '22' => [6, 14],
                '23' => [7, 15],
                '24' => [8, 16],
                '25' => [17, 21],
                '26' => [18, 22],
                '27' => [19, 23],
                '28' => [20, 24],
                '29' => [-26, -28],
                '30' => [-25, -27],
                '31' => [25, 27],
                '32' => [26, 28],
                '33' => [29, -32],
                '34' => [30, -31],
                '35' => [31, 32]
            ],
            '64' => [
                '33' => [1, 17],
                '34' => [2, 18],
                '35' => [3, 19],
                '36' => [4, 20],
                '37' => [5, 21],
                '38' => [6, 22],
                '39' => [7, 23],
                '40' => [8, 24],
                '41' => [9, 25],
                '42' => [10, 26],
                '43' => [11, 27],
                '44' => [12, 28],
                '45' => [13, 29],
                '46' => [14, 30],
                '47' => [15, 31],
                '48' => [16, 32],
                '49' => [33, 41],
                '50' => [34, 42],
                '51' => [35, 43],
                '52' => [36, 44],
                '53' => [37, 45],
                '54' => [38, 46],
                '55' => [39, 47],
                '56' => [40, 48],
                '57' => [49, 53],
                '58' => [50, 54],
                '59' => [51, 55],
                '60' => [52, 56],
                '61' => [-58, -60],
                '62' => [-57, -59],
                '63' => [57, 59],
                '64' => [58, 60],
                '65' => [61, -64],
                '66' => [62, -63],
                '67' => [63, 64]
            ],
        ];
        $programs = $competition->programs->where('competition_system', 'erm');
        foreach ($programs as $program) {
            foreach ($riseFromErm[$program->chart_size] as $seq => $rise) {
                $bouts = Bout::where('program_id', $program->id)->where('in_program_sequence', $seq)->update([
                    'white_rise_from' => $rise[0],
                    'blue_rise_from' => $rise[1]
                ]);
            };
        };
        //kos
        $riseFromKos = [
            '4' => [
                '3' => [1, 2],
            ],
            '8' => [
                '5' => [1, 3],
                '6' => [2, 4],
                '7' => [5, 6]
            ],
            '16' => [
                '9' => [1, 5],
                '10' => [2, 6],
                '11' => [3, 7],
                '12' => [4, 8],
                '13' => [9, 11],
                '14' => [10, 12],
                '15' => [13, 14]
            ],
            '32' => [
                '17' => [1, 9],
                '18' => [2, 10],
                '19' => [3, 11],
                '20' => [4, 12],
                '21' => [5, 13],
                '22' => [6, 14],
                '23' => [7, 15],
                '24' => [8, 16],
                '25' => [17, 21],
                '26' => [18, 22],
                '27' => [19, 23],
                '28' => [20, 24],
                '29' => [25, 27],
                '30' => [26, 28],
                '31' => [29, 30]
            ],
            '64' => [
                '33' => [1, 17],
                '34' => [2, 18],
                '35' => [3, 19],
                '36' => [4, 20],
                '37' => [5, 21],
                '38' => [6, 22],
                '39' => [7, 23],
                '40' => [8, 24],
                '41' => [9, 25],
                '42' => [10, 26],
                '43' => [11, 27],
                '44' => [12, 28],
                '45' => [13, 29],
                '46' => [14, 30],
                '47' => [15, 31],
                '48' => [16, 32],
                '49' => [33, 41],
                '50' => [34, 42],
                '51' => [35, 43],
                '52' => [36, 44],
                '53' => [37, 45],
                '54' => [38, 46],
                '55' => [39, 47],
                '56' => [40, 48],
                '57' => [49, 53],
                '58' => [50, 54],
                '59' => [51, 55],
                '60' => [52, 56],
                '61' => [57, 59],
                '62' => [58, 60],
                '63' => [61, 62]
            ],
        ];
        $programs = $competition->programs->where('competition_system', 'kos');
        foreach ($programs as $program) {
            foreach ($riseFromKos[$program->chart_size] as $seq => $rise) {
                $bouts = Bout::where('program_id', $program->id)->where('in_program_sequence', $seq)->update([
                    'white_rise_from' => $rise[0],
                    'blue_rise_from' => $rise[1]
                ]);
            };
        };


        return response()->json($program);
    }

    public function updateSequence(Request $request)
    {
        $validated = $request->validate([
            '*.id' => 'required',
            '*.mat' => 'required',
            '*.date' => 'required',
            '*.section' => 'required',
            '*.sequence' => 'required',
        ]);
        collect($validated)->each(function (array $val) {
            $program = Program::find($val['id']);
            
            $program->update($val);

            $program->bouts()->update([
                'mat' => $val['mat'],
                'date' => $val['date'],
                'section' => $val['section']
            ]);
        });

        return redirect()->back();
    }

    public function lockSeat(Competition $competition)
    {
        $service = (new BoutGenerationService($competition));

        $service->invalidateByeBouts();
        // dd('aaaa');
        $service->resequence();

        $competition->programs()->each(function (Program $program) {
            $program->confirmDraw();
        });

        $competition->update(['status' => 3]);

        return redirect()->back();
    }

    public function programsUpdate(Competition $competition, Request $request)
    {
        $programs = $request->all();
        // dd($programs);
        foreach ($programs as $p) {
            $program = Program::where('id', $p['id'])->first();
            $program->update(['competition_system' => $p['competition_system']]);
            $program->updateChartSize();
            $program->save();
        }
    }

    public function medalQuantityExport(Competition $competition)
    {        
        $fileName = '獎牌數量表_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new MedalQuantity($competition), $fileName);
    }

    public function programTimeExport(Competition $competition)
    {
        $fileName = $competition->name . '項目時間表.xlsx';

        return Excel::download(new ProgramTimeExport($competition), $fileName);
    }

    public function generateOnlineTable(Competition $competition, Program $program)
    {
        $settings = $this->tcpdfSetting($program);
        // $fontService = new FontService;
        // $file = $fontService->addFont('resources/fonts/NotoSerifCJKhk-Bold.ttf');
        // dd($file);
        $settings['service']->pdf(
            $settings['players'],
            $this->generateWinnersArray($program),
            $this->generateSequencesArray($program),
            $this->generateWinnerList($competition,$program->athletes),
            [ 
                'program' => $program->converGender() . $program->competitionCategory->name ,
                'athletes_count' => $program->athletes->count(),
                'weight' => $program->convertWeight(),
                'mat' => $program->mat,
                'date' => $program->date,
                'section' => $program->section,
            ],
            $settings['repechagePlayers'],
        );
    }

    // CompetitionController.php
    public function generateAllProgramsOnlineTable(Competition $competition,$winnerLine = false)
    {
        $pdf = new CustomTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(false);
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetAutoPageBreak(TRUE, 0);
        foreach($competition->programs as $program){
            $settings = $this->tcpdfSetting($program);
            $pdf = $settings['service']->multiPdf(
                $pdf,
                $settings['players'],
                $this->generateWinnersArray($program),
                $this->generateSequencesArray($program),
                $this->generateWinnerList($competition,$program->athletes),
                [ 
                    'program' => $program->converGender() . $program->competitionCategory->name ,
                    'athletes_count' => $program->athletes->count(),
                    'weight' => $program->convertWeight(),
                    'mat' => $program->mat,
                    'date' => $program->date,
                    'section' => $program->section,
                ],
                $settings['repechagePlayers'],
            );
        }
        $pdf->Output($competition->name . '所有上線表.pdf', 'I');
    }
    /**
     * 生成序列號
     */
    public function generateWinnerList($program)
    {
        $winner_list = [];
        
        // 使用 Collection 的 filter 方法過濾已確定排名的運動員
        if($program->status == 0){

        }
        $programAthletes = $program->programAthletes()->whereIn('rank',[1,2,3])->where('is_weight_passed',1)->orderBy('rank')->get();
        // 生成獲獎者列表
        for ($i = 0; $i < count($programAthletes); $i++) {
            $award = $i + 1;
            
            // 如果是第4個且award_count為4，則獎項設為3（並列第三）
            if ($i == 3 && count($programAthletes) == 4) {
                $award = 3;
            }
            
            $athlete_name = '';
            $is_determined = false;
            
            if ($programAthletes[$i]) {
                $athlete_name = $this->smartTruncate($programAthletes[$i]->athlete->name) . $this->smartTruncate($programAthletes[$i]->athlete->name_secondary);
                $is_determined = true;
            } else {
                // 如果該名次尚未確定，顯示待定或空白
                $athlete_name = '待定';
            }
            
            $winner_list[] = [
                'award' => $award,
                'name' => $athlete_name,
                'is_determined' => $is_determined
            ];
        }
        
        return $winner_list;
    }

    /**
     * 從 program 取得選手資料
     */
    private function getPlayersFromProgram(Program $program)
    {
        return $program?->bouts->map(function ($bout, $index) use ($program) {
            if ($index > ($program->chart_size / 2 - 1)) {
                return;
            } else {
                return [
                    'white' => ['name' => $bout->white_player->name ?? '' , 'name_secondary' => $bout->white_player->name_secondary ?? '', 'team' => ($bout->white_player->team->abbreviation ? $bout->white_player->team->abbreviation . '-' : '') . $bout->white_player->team->name ?? '' , 'is_weight_passed' => $bout->whiteAthlete->is_weight_passed ?? ''],
                    'blue' => ['name' => $bout->blue_player->name ?? '', 'name_secondary' => $bout->blue_player->name_secondary ?? '', 'team' =>  $bout->blue_player->team ? (($bout->blue_player->team->abbreviation ? $bout->blue_player->team->abbreviation . '-' : '') . $bout->blue_player->team->name ?? '') : '' , 'is_weight_passed' => $bout->blueAthlete->is_weight_passed ?? '']
                ];
            }
        })->reject(function ($bout) {
            return empty($bout);
        }) ?? collect([]);
    }

    private function generateWinnersArray(Program $program)
    {
        $bouts = $program->bouts()->orderBy('in_program_sequence')->get();
        
        if ($bouts->isEmpty()) {
            return $this->getDefaultWinnersArray($program->chart_size);
        }
        
        $rounds = $bouts->groupBy('round');
        $winners = [];
        
        // 按輪次降序排序（round大的在上面）
        $sortedRounds = $rounds->sortKeysDesc();
        
        foreach ($sortedRounds as $round => $roundBouts) {
            $roundWinners = [];
            
            // 在每個輪次內按照 in_program_sequence 排序
            $sortedBouts = $roundBouts->sortBy('in_program_sequence');
            
            foreach ($sortedBouts as $bout) {
                if ($bout->status != 0 && $bout->winner === $bout->white) {
                    $roundWinners[] = 1;
                } elseif ($bout->status != 0 && $bout->winner === $bout->blue) {
                    $roundWinners[] = 2;
                } else {
                    $roundWinners[] = 0;
                }
            }
            
            $winners[] = $roundWinners;
        }
        
        return $winners;
    }

    /**
     * 生成預設的勝者陣列（當沒有比賽資料時使用）
     */
    private function getDefaultWinnersArray($chartSize)
    {
        // 根據圖表大小生成預設結構
        $rounds = log($chartSize, 2);
        $winners = [];
        
        $matchesInRound = $chartSize / 2;
        for ($i = 0; $i < $rounds; $i++) {
            $roundWinners = array_fill(0, $matchesInRound, 0); // 全部設為0（無結果）
            $winners[] = $roundWinners;
            $matchesInRound /= 2;
        }
        
        return $winners;
    }

    private function generateSequencesArray(Program $program)
    {
        // 根據比賽輪次生成序列號
        $size = $program->chart_size;
        $rounds = log($size, 2); // 計算輪次
        
        $sequences = [];
        $currentNum = 1;
        
        for ($round = 0; $round < $rounds; $round++) {
            $matchesInRound = $size / pow(2, $round + 1);
            $roundSequences = [];
            
            for ($match = 0; $match < $matchesInRound; $match++) {
                $roundSequences[] = $currentNum++;
            }
            
            $sequences[] = $roundSequences;
        }
        
        return $sequences;
    }

    public function generateCert(Competition $competition, Program $program)
    {
        // 获取排名 1-3 的运动员
        $athletes = $program->programAthletes()
            ->whereIn('rank', [1, 2, 3])
            ->where('is_weight_passed', 1)
            ->orderBy('rank')
            ->get();

        // 创建 PDF - 改為縱向 (P)
        $pdf = new TCPDF('P', 'pt', 'A4', true, 'UTF-8', false);
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('notoserifcjkhk', '', 30);
        
        $pageWidth = 595; // A4 纵向宽度 (pt)
        
        foreach ($athletes as $athlete) {
            $pdf->AddPage();
            
            $weight = $program->convertWeight();
            $rank = match((int)$athlete->rank) {
                1 => '一', 2 => '二', 3 => '三', default => (string)$athlete->rank
            };
            
            $gender = $athlete->athlete?->gender == 'M' ? '男子' : '女子';
            $categoryName = $program->competitionCategory->name ?? '';
            $athleteName = $athlete->athlete?->name ?? '';
            
            // 第一行：組別（置中）
            $firstLineText = '組別：' . $gender . $categoryName;
            $firstLineWidth = $pdf->GetStringWidth($firstLineText);
            $firstLineX = ($pageWidth - $firstLineWidth) / 2;
            
            $pdf->SetXY($firstLineX, 280);
            $pdf->Cell($firstLineWidth, 40, $firstLineText, 0, 0, 'L');
            
            // 下面三行跟隨第一行的 X 座標
            $followX = $firstLineX;
            
            // 第二行：級別
            $secondLineText = '級別：' . strtoupper($weight);
            $pdf->SetXY($followX, 360);
            $pdf->Cell($pdf->GetStringWidth($secondLineText), 40, $secondLineText, 0, 0, 'L');
            
            // 第三行：姓名
            $thirdLineText = '姓名：';
            $pdf->SetXY($followX, 440);
            $pdf->Cell($pdf->GetStringWidth($thirdLineText), 40, $thirdLineText, 0, 0, 'L');

            $pdf->SetXY($followX + 90, 440);
            if(strlen($athleteName) > 16){
                $pdf->SetFont('notoserifcjkhk', '', 16);
                $pdf->Cell($pdf->GetStringWidth($athleteName), 40, $athleteName , 0, 0, 'L');
            }else {
                $pdf->Cell($pdf->GetStringWidth($athleteName), 40, $athleteName , 0, 0, 'L');
            }

            $pdf->SetFont('notoserifcjkhk', '', 30);
            // 第四行：名次
            $fourthLineText = '名次：第' . $rank . '名';
            $pdf->SetXY($followX, 520);
            $pdf->Cell($pdf->GetStringWidth($fourthLineText), 40, $fourthLineText, 0, 0, 'L');
        }
        
        $filename = 'certificates_' . $program->id . '_' . date('YmdHis') . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }
    private function smartTruncate($name, $maxLength = 21)
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

    public function getRepechagePlayers($program){
        $round3Bouts = $program?->bouts->filter(function ($bout) {
            return $bout->round == 3;
        });
        $round2Bouts = $program?->bouts->filter(function ($bout) {
            return $bout->round == 2;
        });
        return [
            $round3Bouts->map(function ($bout) {
                return [
                    'white' => ['name_display' => $bout->white_player->name_display ?? '', 'from' => ''],
                    'blue' => ['name_display' => $bout->blue_player->name_display ?? '', 'from' => ''],
                ];
            })->values()->all(),
            $round2Bouts->map(function ($bout) {
                return [
                    'blue' => ['name_display' => $bout->white_player->name_display ?? '', 'from' => ''],
                    'blue' => ['name_display' => $bout->blue_player->name_display ?? '', 'from' => ''],
                ];
            })->values()->all()
        ];
    }

    public function tcpdfSetting($program){
        switch($program->competition_system){
            case 'kos':
                $settings = File::json(storage_path('setting/game_tournament_knockout.json'));
                $service = new TournamentQuarterService($settings);
                $players = $this->getPlayersFromProgram($program);
                $repechagePlayers = null;
                break;
            case 'rrb':
                $settings = File::json(storage_path('setting/game_round_robbin_option2.json'));
                $service = new RoundRobbinOption2Service($settings);
                $players = $program->athletes;
                $repechagePlayers = null;
                break;
            case 'erm':
                $settings = File::json(storage_path('setting/game_tournament_quarter.json'));
                $service = new TournamentQuarterService($settings);
                $players = $this->getPlayersFromProgram($program);
                $repechagePlayers = $this->getRepechagePlayers($program);
                break;
        }
        $service->setFonts('notoserifcjkhk', 'notoserifcjkhk', 'notoserifcjkhk');
        $service->setTitles($program->competition->name, $program->competition->name_secondary);

        return [
            'settings' => $settings,
            'service' => $service,
            'players' => $players,
            'repechagePlayers' => $repechagePlayers,
        ];
    }
}
