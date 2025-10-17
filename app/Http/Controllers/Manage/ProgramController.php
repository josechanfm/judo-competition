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
use App\Services\Printer\RoundRobbinOption1Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use PgSql\Lob;
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
        $competition->programsBouts;
        
        $competition->bouts()->delete();
        // TODO: generate bouts
        $competition->generateBouts();

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
        // dd($validated);
        collect($validated)->each(function (array $val) {
            Program::where('id', $val['id'])->update($val);
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
        $fileName = '項目時間表_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ProgramTimeExport($competition), $fileName);
    }

    public function generateOnlineTable(Competition $competition, Program $program)
    {
        $size = $program->chart_size;

        switch($program->competition_system){
            case 'kos':
                $settings = File::json(storage_path('setting/game_tournament_knockout.json'));
                $service = new TournamentQuarterService($settings);
                break;
            case 'rrb':
                $settings = File::json(storage_path('setting/game_round_robbin_option1.json'));
                $service = new RoundRobbinOption1Service($settings);
        }
        
        $players = $program?->bouts->map(function ($bout, $index) use ($size) {
            if ($index > ($size / 2 - 1)) {
                return;
            } else {
                return [
                    'white' => ['name' => $bout->white_player->name ?? '' , 'name_secondary' => $bout->white_player->name_secondary ?? '', 'team' => $bout->white_player->team->name],
                    'blue' => ['name' => $bout->blue_player->name ?? '', 'name_secondary' => $bout->blue_player->name_secondary ?? '', 'team' => $bout->blue_player->team->name]
                ];
            }
        })->reject(function ($bout) {
            return empty($bout);
        }) ?? null;

        $service->setFonts('NotoSerifTC', 'NotoSerifTC', 'cid0ct');
        $service->setTitles($program->competition->name, null);

        $service->pdf(
            $players,
            [
                [1, 1, 1, 1],
                [1, 1],
                [1],
            ],
            [],
            $this->generateWinnerList($competition,$program->athletes),
            [ 
                'program' => $program->converGender() . $program->competitionCategory->name ,
                'athletes_count' => $program->athletes->count(),
                'weight' => $program->convertWeight()
            ]
        );
    }

    // CompetitionController.php
    public function generateAllProgramsOnlineTable(Competition $competition)
    {
        $programs = $competition->programs;
        
        if ($programs->isEmpty()) {
            return response()->json(['error' => '沒有找到任何量級'], 404);
        }
        
        $firstProgram = $programs->first();
        $settings = $this->getSettingsBySystem($firstProgram->competition_system);
        $service = $this->getServiceBySystem($firstProgram->competition_system, $settings);
        
        $service->setFonts('NotoSerifTC', 'NotoSerifTC', 'NotoSerifTC');
        $service->setTitles($competition->name,null);
        
        // 準備所有 program 的數據
        $allProgramsData = [];
        foreach ($programs as $program) {
            $players = $this->getPlayersFromProgram($program);
            $allProgramsData[] = [
                'players' => $players->toArray(),
                'winners' => [],
                'sequences' => [],
                'winnerList' => $this->generateWinnerList($competition, $program->athletes),
                'ellipseData' => [ 
                    'program' => $program->converGender() . $program->competitionCategory->name,
                    'athletes_count' => $program->athletes->count(),
                    'weight' => $program->convertWeight()
                ],
                'repechagePlayers' => [],
                'repechage' => false
            ];
            // dd($allProgramsData);
        }
        // dd($allProgramsData);
        // 如果 service 有 multiplePrograms 方法就使用，否則使用第一個
        if (method_exists($service, 'pdfForMultiplePrograms')) {
            $service->pdfForMultiplePrograms($allProgramsData);
        } else {
            // 回退方案：只生成第一個 program
            $firstProgramData = $allProgramsData[0];
            $service->pdf(
                $firstProgramData['players'],
                $firstProgramData['winners'],
                $firstProgramData['sequences'],
                $firstProgramData['winnerList'],
                $firstProgramData['ellipseData'],
                $firstProgramData['repechagePlayers'],
                $firstProgramData['repechage']
            );
        }
    }
    /**
     * 生成序列號
     */
    public function generateWinnerList($competition, $athletes)
    {
        $winner_list = [];
        $athlete_count = count($athletes);
        
        // 根據 awarding_method 決定獲獎人數
        if ($competition->competition_type->awarding_methods == 0) {
            // awarding_method == 0: 運動員數量 - 1，最大為4
            $award_count = min($athlete_count - 1, 4);
        } else {
            // awarding_method == 1: 運動員數量，最大為4
            $award_count = min($athlete_count, 4);
        }

        // 確保至少有一個獲獎者
        $award_count = max($award_count, 1);
        
        // 生成獲獎者列表
        for ($i = 0; $i < $award_count; $i++) {
            $award = $i + 1;
            
            // 如果是第4個且award_count為4，則獎項設為3（並列第三）
            if ($i == 3 && $award_count == 4) {
                $award = 3;
            }
            
            $winner_list[] = [
                'award' => $award,
                'name' => ''
            ];
        }
        
        return $winner_list;
    }

    private function getSettingsBySystem($system)
    {
        switch($system) {
            case 'kos':
                return File::json(storage_path('setting/game_tournament_knockout.json'));
            case 'rrb':
                return File::json(storage_path('setting/game_round_robbin_option1.json'));
            default:
                return File::json(storage_path('setting/game_tournament_knockout.json'));
        }
    }

    /**
     * 根據競賽系統取得服務
     */
    private function getServiceBySystem($system, $settings)
    {
        switch($system) {
            case 'kos':
                return new TournamentQuarterService($settings);
            case 'rrb':
                return new RoundRobbinOption1Service($settings);
            default:
                return new TournamentQuarterService($settings);
        }
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
                    'white' => ['name' => $bout->white_player->name ?? '' , 'name_secondary' => $bout->white_player->name_secondary ?? '', 'team' => $bout->white_player->team->name ?? ''],
                    'blue' => ['name' => $bout->blue_player->name ?? '', 'name_secondary' => $bout->blue_player->name_secondary ?? '', 'team' => $bout->blue_player->team->name ?? '']
                ];
            }
        })->reject(function ($bout) {
            return empty($bout);
        }) ?? collect([]);
    }
}
