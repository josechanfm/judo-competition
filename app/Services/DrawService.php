<?php

namespace App\Services;

use App\Models\Program;
use Illuminate\Support\Facades\Log;

class DrawService
{
    protected Program $program;

    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Get the sequence for assigning seed player
     *
     * @param $num
     * @return int[]
     */
    private function getEMap($num): array
    {
        /** IJF 2022 version **/
        /* https://78884ca60822a34fb0e6-082b8fd5551e97bc65e327988b444396.ssl.cf3.rackcdn.com/up/2022/03/IJF_Sport_and_Organisation_Rul-1646858825.pdf */
        $eMap = [
            2 => [0, 1],
            4 => [0, 2, 3, 1],
            8 => [0, 4, 6, 2, 3, 7, 5, 1],
            16 => [0, 8, 12, 4, 6, 14, 10, 2, 3, 11, 15, 7, 5, 13, 9, 1],
            32 => [0, 16, 24, 8, 12, 28, 20, 4, 6, 22, 30, 14, 10, 26, 18, 2, 3, 19, 27, 11, 15, 31, 23, 7, 5, 21, 29, 13, 9, 25, 17, 1],
            64 => [0, 32, 48, 16, 24, 56, 40, 8, 12, 44, 60, 28, 20, 52, 36, 4, 6, 38, 54, 22, 30, 62, 46, 14, 10, 42, 58, 26, 18, 50, 34, 2, 3, 35, 51, 19, 27, 59, 43, 11, 15, 47, 63, 31, 23, 55, 39, 7, 5, 37, 53, 21, 29, 61, 45, 13, 9, 41, 57, 25, 17, 49, 33, 1]
        ];

        return $eMap[$num];
    }

    /**
     * How many of the athletes are seed
     *
     * @param $arr
     * @return int
     */
    private function getSeedSize($arr)
    {
        $cnt = 0;
        foreach ($arr as $a) {
            if ($a['seed']) {
                $cnt++;
            }
        }
        return $cnt;
    }

    /**
     * Handle draw athletes
     *
     * @return array
     */
    public function draw(): array
    {
        switch ($this->program->contest_system) {
            case Program::ERM:
                return $this->drawERM();
            case Program::RRB:
            case Program::RRBA:
                return $this->drawRRB();
            case Program::KOS:
                return $this->drawKOS();
            default:
                throw new \Exception('Invalid contest system');
        }
    }

    /**
     * We use KOS drawing method since they are the same
     *
     * @return array
     */
    private function drawERM(): array
    {
        return $this->drawKOS();
    }

    private function getAthletes(): array
    {
        return $this->program
            ->athletes()
            ->with('athlete.team')
            ->get()
            ->toArray();
    }

    /**
     * Shuffle players and move seed to front
     *
     * @param $players
     * @return mixed
     */
    private function shuffle($players): mixed
    {
        foreach ($players as $i => $player) {
            $players[$i]['bout_seq'] = $i;
        }
        shuffle($players);
        $playerCount = count($players);
        $cnt = 0;
        foreach ($players as $i => $p) {
            if ($p['seed']) {
                $tmp = $players[$cnt];
                $players[$cnt] = $players[$i];
                $players[$i] = $tmp;
                $cnt++;
            }
        }
        return $players;
    }

    /**
     * @return array|mixed
     */
    private function drawRRB(): mixed
    {
        $athletes = $this->getAthletes();

        //        foreach ($athletes as $key => $athlete) {
        //            $athlete->opponent_id = $athletes[$key + 1]->id ?? null;
        //            $athlete->save();
        //        }

        $athletes = $this->shuffle($athletes);
        //assign bout position for players
        //display
        foreach ($athletes as $i => $p) {
            $athletes[$i]['seat'] = $i + 1;
        }
        //sort by Players sequence
        $columns = array_column($athletes, 'bout_seq');
        array_multisort($columns, SORT_ASC, $athletes);

        return $athletes;
    }

    /**
     * @return int
     */
    private function getChartSize(): int
    {
        return $this->program->chart_size;
    }

    /**
     * @return array
     */
    private function drawKOS()
    {
        $players = $this->shuffle($this->getAthletes());
        $playerCount = sizeof($players);
        $gameSize = $this->getChartSize($players);
        $seedCount = $this->getSeedSize($players);

        $playList = array_fill(0, $gameSize - 1, null);
        $playSequence = $this->getEMap($gameSize);

        $chunk2 = array_chunk($playSequence, sizeof($playSequence) / 2);

        //assign upper bout players
        foreach ($chunk2[0] as $i) {
            $playList[$i] = array_shift($players);
        }

        $i = 0;

        while (count($players) > 0) {
            $playList[$chunk2[1][$i++]] = array_shift($players);
        }

        //remove empty element/null in the playerList
        $playList = array_filter($playList, function ($value) {
            return !is_null($value);
        });
        //assign bout position for players
        //display
        foreach ($playList as $i => $p) {
            $playList[$i]['seat'] = $i + 1;
        }
        //sort by Players sequence
        $columns = array_column($playList, 'bout_seq');
        array_multisort($columns, SORT_ASC, $playList);

        return $playList;
    }

    /**
     * @param $playList
     * @return array
     */
    private function shuffleBout($playList): array
    {
        $chunk2 = array_chunk($playList, 2);
        foreach ($chunk2 as $c) {
            if (isset($c[0]) && $c[0]['seed'] <= 0) {
                shuffle($c);
            }
            $pList[] = $c[0];
            $pList[] = $c[1];
        }
        return $pList;
    }

    /**
     * @param $arr
     * @param $playerCount
     * @param $seedCount
     * @return array
     */
    private function unset_chunk2Lower($arr, $playerCount, $seedCount): array
    {
        $arrSize = count($arr) * 2;
        $blank = $arrSize / 2 - ($playerCount - $arrSize / 2);
        $blank = $blank >= 4 ? $seedCount : $blank;
        $seedLower = array(0, $arrSize / 4, $arrSize / 8, $arrSize / 4 + 1);
        for ($i = 0; $i < $blank; $i++) {
            unset($arr[$seedLower[$i]]);
        }
        return array_values($arr);
    }
}
