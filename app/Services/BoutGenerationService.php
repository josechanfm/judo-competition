<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Bout;
use App\Models\Competition;
use App\Models\Program;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BoutGenerationService
{
    private Competition $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    /**
     * 獲取 RRBA 的 Bout Schema
     *
     * @return array
     */
    private function getPromotionRRBA(): array
    {
        return $this->populateBoutsFromChart([
            8 => [[1, 3], [2, 4]],
            4 => [[1, 5], [3, 5]],
            2 => [[0, 0]],
            1 => [[0, 0]]
        ]);
    }

    /**
     * 根據 Chart Size 獲取 RRB 的 Bout Schema
     *
     * @param $chartSize
     * @return array
     */
    private function getPromotionRRB($chartSize): array
    {
        $arr = array(
            5 => array(
                5 => array([2, 5], [3, 4]),
                4 => array([1, 5], [2, 3]),
                3 => array([1, 2], [4, 5]),
                2 => array([1, 3], [2, 4]),
                1 => array([1, 4], [3, 5]),
            ),
            4 => array(
                3 => array([1, 2], [3, 4]),
                2 => array([1, 3], [2, 4]),
                1 => array([1, 4], [2, 3]),
            ),
            3 => array(
                3 => array([1, 2]),
                2 => array([1, 3]),
                1 => array([2, 3]),
            ),
            2 => array(
                2 => array([1, 2])
            ),
            1 => array(
                2 => array([1, 1])
            ),
            0 => array()
        );

        return $this->populateBoutsFromChart($arr[$chartSize]);

        // foreach($data as $i=>$d){
        // 	echo json_encode($d);
        // 	echo '<br>';
        // }
        // echo '<hr>';
    }

    /**
     * 從 RRB 表格 或 RRBA 中產生出 Bout
     * @param $chart
     * @return array
     */
    private function populateBoutsFromChart($chart): array
    {
        $rise = [];
        foreach ($chart as $round => $bouts) {
            foreach ($bouts as $r) {
                $rise[] = array(
                    'round' => $round,
                    'white' => $r[0],
                    'blue' => $r[1],
                    // 'white'=>$ath[$r[0]-1]->athlete_id,
                    // 'blue'=>$ath[$r[1]-1]->athlete_id,
                );
            }
        }
        return $rise;
    }


    /**
     * 獲得復活賽的 Bout Schema
     *
     * @param $chartSize
     * @return array
     */
    private function getPromotionERM($chartSize, $repechageNumber): array
    {
        //        ///普級
        $totalBouts = $chartSize + 2; //10

        if ($chartSize === 4) {
            $rise[$totalBouts - 1] = [
                'round' => 2,
                'turn' => 2,
                'white_rise' => -1,
                'blue_rise' => -2,
            ];

            return $rise;
        }
        //        $winner = 1; //1
        //        $startPoint = $chartSize / 2; //5
        //
        //        for ($j = 0; $j < $startPoint - 2; $j++) { //-1
        //            $rise[$startPoint + $j]['white_rise'] = $winner++;
        //            $rise[$startPoint + $j]['blue_rise'] = $winner++;
        //        }

        //repechage first round
        if ($repechageNumber == 1) {
            $rise[$totalBouts - 6]['white_rise'] = ($totalBouts - 9) * -1;
            $rise[$totalBouts - 6]['blue_rise'] = ($totalBouts - 8) * -1;
            $rise[$totalBouts - 6]['round'] = 3;
            $rise[$totalBouts - 6]['turn'] = 4;
            $rise[$totalBouts - 5]['white_rise'] = ($totalBouts - 7) * -1;
            $rise[$totalBouts - 5]['blue_rise'] = ($totalBouts - 6) * -1;
            $rise[$totalBouts - 5]['round'] = 3;
            $rise[$totalBouts - 5]['turn'] = 4;
        }
        // //repechage second round
        if ($repechageNumber == 2) {
            $rise[$totalBouts - 2]['white_rise'] = ($totalBouts - 5);
            $rise[$totalBouts - 2]['round'] = 2;
            $rise[$totalBouts - 2]['turn'] = 2;
            $rise[$totalBouts - 2]['blue_rise'] = ($totalBouts - 2) * -1;

            $rise[$totalBouts - 1]['white_rise'] = ($totalBouts - 4);
            $rise[$totalBouts - 1]['blue_rise'] = ($totalBouts - 3) * -1;
            $rise[$totalBouts - 1]['round'] = 2;
            $rise[$totalBouts - 1]['turn'] = 2;
        }
        //        // //final
        //        $rise[$totalBouts]['white_rise'] = $totalBouts - 5;
        //        $rise[$totalBouts]['blue_rise'] = $totalBouts - 4;
        //        $rise[$totalBouts]['round'] = -8;

        return $rise;
    }

    /**
     * 使所有輪空場次無效，及自動晉級
     *
     * @return void
     */
    public function invalidateByeBouts($date = null, $status = 0): void
    {

        $this->getSections()->each(function (array $section) use ($status, $date) {
            if ($date == null || $section['date'] == $date) {
                $bouts = $this->getBoutsUnderSection(...$section);
                $bouts->each(function (Bout $bout) use ($status) {
                    if ($bout->isWhiteBye() || $bout->isBlueBye()) {
                        $bout->byeRise($status);
                    }
                });
            }
        });
    }

    /**
     * 使所有至少有一方未過體重場次無效
     *
     * @return void
     */
    public function invalidateWeightBouts($date): void
    {
        $this->getSections()->each(function (array $section) use ($date) {
            if ($section['date'] == $date) {
                $bouts = $this->getBoutsUnderSection(...$section);

                // todo: only process outer layer
                $bouts->where('queue', '!=', 0)->each(function (Bout $bout) {
                    $bout->load('whiteAthlete', 'blueAthlete');
                    if (
                        ($bout->white > 0 && $bout->blue > 0) &&
                        (!$bout->whiteAthlete->is_weight_passed &&
                            !$bout->blueAthlete->is_weight_passed)
                    ) {
                        $bout->cancel();
                    } else if (($bout->white > 0 && $bout->blue > 0) &&
                        (!$bout->whiteAthlete->is_weight_passed)
                    ) {
                        $bout->cancel($bout->blue);
                    } else if (($bout->white > 0 && $bout->blue > 0) &&
                        (!$bout->blueAthlete->is_weight_passed)
                    ) {
                        $bout->cancel($bout->white);
                    }
                });
            }
        });
    }

    /**
     * 獲得 KOS 的 Bout Schema
     *
     * @param $chartSize
     * @return array
     */
    private function getPromotionKOS($chartSize)
    {
        ///普級
        $totalBouts = $chartSize - 2; //10
        $winner = 1;
        $startPoint = $chartSize / 2; //5

        for ($j = 0; $j < $startPoint - 2; $j++) {
            $rise[$startPoint + $j]['white_rise'] = $winner++;
            $rise[$startPoint + $j]['blue_rise'] = $winner++;
        }

        // //final
        $rise[$totalBouts]['white_rise'] = $totalBouts - 1;
        $rise[$totalBouts]['blue_rise'] = $totalBouts;
        // echo json_encode($rise);
        return $rise;
    }

    /**
     * 生成賽程
     *
     * @return void
     */
    public function generate(): void
    {
        $sections = $this->getSections();

        $sections->each(function ($section) {
            $programs = $this->getProgramsUnderSection(...$section);

            $programs->each(function ($program) {
                $this->generateForProgram($program);
            });

            $this->setSequenceAndQueueForBoutsInSection($programs, ...$section);
        });
    }

    /**
     * Reset queue when bout is deleted
     *
     * @return void
     */
    public function resequence($date = null): void
    {
        $this->getSections()->each(function ($section) use ($date) {
            if ($date == null || $section['date'] == $date) {
                $programs = $this->getProgramsUnderSection(...$section);

                $this->cleanUpQueueForBoutsInSection($programs, ...$section);
            }
        });
    }

    public function assignAthletesToBouts(Program $program): void
    {

        $athletes = $program->programAthletes();

        switch ($program->contest_system) {
            case Program::KOS:
                $this->assignAthletesToKOS($program, $athletes);
                break;
            case Program::RRB:
            case PROGRAM::RRBA:
                $this->assignAthletesToRRB($program, $athletes);
                break;
            case Program::ERM:
                $this->assignAthletesToERM($program, $athletes);
                break;
        }
    }

    private function assignAthletesToKOS($program, $athletes, $isRepechage = 0): void
    {
        if ($isRepechage == 1) {
            $maxTurn = 2;
        } else {
            $maxTurn = 0;
        }
        for ($chart_size = $program->chart_size; $chart_size !== 1; $chart_size = $chart_size / 2) {
            $maxTurn++;
        }
        $program->bouts()->where('turn', $maxTurn)->update([
            'white' => -1,
            'blue' => -1,
        ]);

        $athletes->each(function ($athlete) use ($program) {
            $seat = $athlete['seat'];
            $color = $seat % 2 === 0 ? 'blue' : 'white';
            $program->bouts->where('in_program_sequence', ceil($seat / 2))->first()->update([
                $color => $athlete['id'] ?? -1,
            ]);
        });
    }

    private function assignAthletesToRRB(Program $program, $athletes): void
    {
        if ($program->contest_system === Program::RRBA) {
            $seatMap = $this->getPromotionRRBA();
        } else {
            $seatMap = $this->getPromotionRRB($program->chart_size);
        }

        $athletes = $athletes->get();

        $program->bouts()->orderBy('in_program_sequence')->get()->each(function (Bout $bout) use ($seatMap, $athletes) {
            $map = $seatMap[$bout->in_program_sequence - 1];
            $bout->update([
                'white' => $athletes->where('seat', $map['white'])->first()->id ?? 0,
                'blue' => $athletes->where('seat', $map['blue'])->first()->id ?? 0,
            ]);
        });
    }

    private function assignAthletesToERM($program, $athletes): void
    {
        $this->assignAthletesToKOS($program, $athletes, 1);
    }

    private function getSections(): Collection
    {
        $days = collect($this->competition->days);
        $section_count = $this->competition->section_number;
        $mat_count = $this->competition->mat_number;

        $sections = collect([]);

        $days->map(function ($day) use (&$sections, $section_count, $mat_count) {
            for ($s = 1; $s <= $section_count; $s++) {
                for ($m = 1; $m <= $mat_count; $m++) {
                    $sections[] = [
                        'date' => $day,
                        'section' => $s,
                        'mat' => $m,
                    ];
                }
            }
        });

        return $sections;
    }

    /**
     * @param $date
     * @param $section
     * @param $mat
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getProgramsUnderSection($date, $section, $mat): \Illuminate\Database\Eloquent\Collection
    {
        return $this->competition->programs()
            ->withCount('athletes')
            ->where('date', $date)
            ->where('section', $section)
            ->where('mat', $mat)
            ->orderBy('sequence')
            ->get();
    }

    /**
     * @param $date
     * @param $section
     * @param $mat
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getBoutsUnderSection($date, $section, $mat): \Illuminate\Database\Eloquent\Collection
    {
        return $this->competition->bouts()
            ->where('bouts.date', $date)
            ->where('bouts.section', $section)
            ->where('bouts.mat', $mat)
            ->orderBy('sequence')
            ->get();
    }

    private function generateForProgram($program): void
    {
        switch ($program->contest_system) {
            case Program::ERM:
                $this->generateForErm($program);
                break;
            case Program::RRB:
            case Program::RRBA:
                $this->generateForRrb($program);
                break;
            case Program::KOS:
                $this->generateForKos($program);
                break;
        }
    }

    /**
     * 生成 ERM 的比賽場次
     *
     * @param $program
     * @return void
     */
    private function generateForErm($program): void
    {
        $this->generateForKos($program, $program->chart_size === 4 ? 1 : 4, 1);
    }

    /**
     * 生成 RRB 的比賽場次
     *
     * @param $program
     * @return void
     */
    private function generateForRrb($program): void
    {
        if ($program->contest_system === Program::RRBA) {
            $schema = $this->getPromotionRRBA();
        } else {
            $schema = $this->getPromotionRRB($program->chart_size);
        }

        $sequence = 1;

        foreach ($schema as $rrbBout) {
            $bout = Bout::fromProgram($program);
            $bout->in_program_sequence = $sequence++;
            $bout->sequence = 0;
            $bout->queue = 0;
            $bout->round = $rrbBout['round'];
            $bout->turn = $rrbBout['round'];
            $bout->white = $rrbBout['white'];
            $bout->blue = $rrbBout['blue'];
            dd($bout);
            $bout->save();
        }
    }

    /**
     * 生成 KOS 的比賽場次
     *
     * @param $program Program 比賽項目
     * @param $repCount int 決賽前穿插的復活賽場次數
     * @return void
     */
    private function generateForKos(Program $program, $repCount = 0, $isRepechage = 0): void
    {
        // sequence in ascending order, starts with 2;
        $sequence = 1;
        if ($isRepechage == 1) {
            $maxRound = 2;
            $maxBout = $program->chart_size + 4;
        } else {
            $maxRound = 0;
            $maxBout = $program->chart_size;
        }
        for ($chart_size = $program->chart_size; $chart_size !== 1; $chart_size = $chart_size / 2) {
            $maxRound++;
        }
        $boutsInPrevDepth = $program->chart_size;
        $boutsInThisDepth = $program->chart_size / 2;
        $firstInThisDepth = 1;


        while ($sequence < $maxBout) {
            $riseFrom = $sequence - $boutsInPrevDepth;
            if ($isRepechage == 1 && $boutsInThisDepth <= 2) {
                $riseFrom = $riseFrom - 2;
            }

            Log::debug("rnd{$boutsInPrevDepth} seq${sequence} bipd${boutsInPrevDepth} bitd${boutsInThisDepth} ${firstInThisDepth}");

            for ($i = 0; $i != $boutsInThisDepth; ++$i) {
                $bout = Bout::fromProgram($program);
                $bout->in_program_sequence = $sequence;
                $bout->sequence = 0;
                $bout->queue = 0;
                $bout->round = $boutsInPrevDepth;
                $bout->turn = $maxRound;
                $bout->white = 0;
                $bout->blue = 0;
                $bout->white_rise_from = max($riseFrom++, 0);
                $bout->blue_rise_from = max($riseFrom++, 0);
                $bout->winner_rise_to = 0;
                $bout->loser_rise_to = 0;
                $bout->save();
                ++$sequence;

                Log::debug("\tgenerating $sequence wrf$bout->white_rise_from brf $bout->blue_rise_from WRT $bout->winner_rise_to");
            }
            if ($boutsInThisDepth == 4 & $isRepechage == 1) {
                $this->generateRepechage($program, 1);
                $maxRound--;
                $sequence = $sequence + 2;
            } else if ($boutsInThisDepth == 2 & $isRepechage == 1) {
                $this->generateRepechage($program, 2);
                $maxRound--;
                $sequence = $sequence + 2;
            }
            $maxRound--;
            // we advance to next level
            $boutsInPrevDepth = $boutsInThisDepth;
            // shrink to half the round
            $boutsInThisDepth /= 2;
            $firstInThisDepth = $sequence;
        };
        $this->moveFinalToLast($program, $repCount);
        // forward winner to next
        $this->PreviousGeneratedBouts($program, $program->bouts);
        // forward loser to next
        $this->touchPreviousGeneratedBouts($program, $program->bouts);
    }

    /**
     * 生成復活賽場次
     *
     * @param $program Program
     * @return void
     */
    private function generateRepechage(Program $program, $repechageNumber): void
    {
        $schema = $this->getPromotionERM($program->chart_size, $repechageNumber);

        if ($repechageNumber == 2) {
            $sequence = $program->chart_size + 1;
        } else if ($repechageNumber == 1) {
            $sequence = $program->chart_size - 3;
        }

        // add erm reps
        foreach ($schema as $ermRepBout) {
            $bout = Bout::fromProgram($program);
            $bout->in_program_sequence = $sequence++;
            $bout->sequence = 0;
            $bout->queue = 0;
            $bout->round = $ermRepBout['round'];
            $bout->turn = $ermRepBout['turn'];
            $bout->white = 0;
            $bout->blue = 0;
            $bout->blue_rise_from = $ermRepBout['blue_rise'];
            $bout->white_rise_from = $ermRepBout['white_rise'];
            $bout->winner_rise_to = 0;
            $bout->loser_rise_to = 0;
            $bout->save();
        }
    }

    private function touchPreviousGeneratedBouts(Program $program, Collection $bouts): void
    {
        $bouts->each(function (Bout $bout) use ($program) {
            // forward loser to next
            // Log::warning('program: ', [$bout->in_program_sequence]);
            $program->bouts()
                ->where(function ($query) use ($bout) {
                    $query->where('in_program_sequence', 0 - $bout->blue_rise_from)
                        ->orWhere('in_program_sequence', 0 - $bout->white_rise_from);
                })
                ->update(['loser_rise_to' => $bout->in_program_sequence]);
        });
    }
    private function PreviousGeneratedBouts(Program $program, Collection $bouts): void
    {
        $bouts->each(function ($bout) use ($bouts) {
            $bout->winner_rise_to = $bouts->filter(function ($nextBout) use ($bout) {
                return $bout->in_program_sequence === $nextBout->blue_rise_from ||
                    $bout->in_program_sequence === $nextBout->white_rise_from;
            })->first()->in_program_sequence ?? 0;

            $bout->save();
        });
    }


    /**
     * @param $programs
     * @param $date
     * @param $section
     * @param $mat
     */
    private function setSequenceAndQueueForBoutsInSection($programs, $date, $section, $mat): void
    {
        $programSequence = $programs->sortBy('sequence')->pluck('sequence', 'id')->toArray();
        $athletes_count = $programs->pluck('athletes_count', 'id')->all();
        $bouts = $this->getBoutsUnderSection($date, $section, $mat);
        $sequence = 1;

        // how many rounds in this section
        $turns = $bouts->unique('turn')
            ->pluck('turn')
            ->sortDesc();
        $maxTurn = $turns->first();
        // sort bouts in each round
        $turns->each(function ($turn) use (&$sequence, $bouts, $athletes_count, $programSequence, $maxTurn) {
            $bouts->where('turn', $turn)
                ->sortBy(function ($bout) use ($programSequence, $athletes_count, $turn, $bouts, $maxTurn) {
                    if ($bouts->where('program_id', $bout->program_id)->where('turn', $maxTurn)->isNotEmpty()) {
                        $hasLastTurn = 1000000;
                    } else {
                        $hasLastTurn = 0;
                    }
                    return $athletes_count[$bout->program_id] * 10000 + $hasLastTurn + $programSequence[$bout->program_id] * 1000 + $bout->in_program_sequence;
                })
                ->each(function ($bout) use (&$sequence) {
                    $bout->sequence = $sequence++;
                    $bout->queue = $bout->sequence;
                    $bout->save();
                });
        });
    }


    /**
     * @param $programs
     * @param $date
     * @param $section
     * @param $mat
     * @return void
     */
    private function moveFinalToLast(Program $program, int $repSize): void
    {
        $final = $program->bouts()->where('in_program_sequence', $program->chart_size - 1 + $repSize)->first();
        $final->round = 1;
        $final->save();
    }
    private function cleanUpQueueForBoutsInSection($programs, $date, $section, $mat): void
    {
        $programSequence = $programs->sortBy('sequence')->pluck('sequence', 'id')->toArray();
        $bouts = $this->getBoutsUnderSection($date, $section, $mat);
        $queue = 1;

        $bouts->where('queue', '!=', 0)
            ->sortBy('queue')
            ->each(function ($bout) use (&$queue) {
                $bout->queue = $queue++;
                $bout->save();
            });
    }
}
