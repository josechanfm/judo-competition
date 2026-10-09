<?php

namespace App\Services;

use App\Models\Program;
use Illuminate\Support\Facades\Log;

class DrawService
{
    /** 一般抽籤：完全隨機（種子選手先抽出位置） */
    public const METHOD_RANDOM = 'random';

    /** 同組織分開：同一隊伍的選手在 bracket 上盡量分開，不要提早相遇 */
    public const METHOD_TEAM_SEPARATED = 'team_separated';

    public const METHODS = [
        self::METHOD_RANDOM,
        self::METHOD_TEAM_SEPARATED,
    ];

    protected Program $program;

    protected string $method;

    public function __construct(Program $program, string $method = self::METHOD_RANDOM)
    {
        $this->program = $program;
        $this->method = in_array($method, self::METHODS, true) ? $method : self::METHOD_RANDOM;
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
        switch ($this->program->competition_system) {
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
            ->programAthletes()
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
        $gameSize = $this->getChartSize();

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

        // 同組織分開：保留種子位置與輪空位置，只重新安排非種子選手的 bracket 位置
        if ($this->method === self::METHOD_TEAM_SEPARATED) {
            $playList = $this->separateTeams($playList);
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
     * 依「隊伍」分散抽籤位置（只適用 KOS / ERM bracket 賽制）。
     *
     * - 只搬動非種子選手：種子選手留在 E-map 指定的位置，輪空(bye)位置也不會被搬走，
     *   所以種子選手的首輪輪空等既有安排不會被破壞。
     * - 成本定義：同隊兩人在第 r 回合相遇 → 成本 1 << (maxRound - r)，越早相遇成本越高。
     *   先用貪婪法（同隊人數多的先排、選成本最低的位置，同成本隨機）分散，
     *   再用隨機交換做區域改進。
     *
     * @param  array  $playList  [position => player|null]
     */
    private function separateTeams(array $playList): array
    {
        $maxRound = $this->maxRound();

        $freePositions = [];   // 可自由安排的位置（非種子、非輪空）
        $movable = [];         // 對應的非種子選手
        $placedTeams = [];     // position => teamKey（含種子選手）

        foreach ($playList as $position => $player) {
            if (is_null($player)) {
                continue; // 輪空位置保持不動
            }

            $teamKey = $this->teamKey($player);

            if (! empty($player['seed'])) {
                if ($teamKey !== null) {
                    $placedTeams[$position] = $teamKey;
                }
                continue;
            }

            $freePositions[] = $position;
            $movable[] = $player;
        }

        if (count($freePositions) < 2) {
            return $playList;
        }

        // 同隊人數多的先排（最難安排），同人數時隨機
        $teamSizes = [];
        foreach ($movable as $player) {
            $teamKey = $this->teamKey($player);
            if ($teamKey !== null) {
                $teamSizes[$teamKey] = ($teamSizes[$teamKey] ?? 0) + 1;
            }
        }

        shuffle($movable);
        usort($movable, function ($a, $b) use ($teamSizes) {
            return ($teamSizes[$this->teamKey($b)] ?? 0) <=> ($teamSizes[$this->teamKey($a)] ?? 0);
        });

        $available = $freePositions;
        $assigned = [];

        foreach ($movable as $player) {
            $teamKey = $this->teamKey($player);
            $bestCost = null;
            $bestPositions = [];

            foreach ($available as $position) {
                $cost = $this->positionCost($placedTeams, $position, $teamKey, $maxRound);

                if ($bestCost === null || $cost < $bestCost) {
                    $bestCost = $cost;
                    $bestPositions = [$position];
                } elseif ($cost === $bestCost) {
                    $bestPositions[] = $position;
                }
            }

            // 成本相同的位置隨機選一個，保留抽籤的隨機性
            $position = $bestPositions[random_int(0, count($bestPositions) - 1)];

            $assigned[$position] = $player;

            if ($teamKey !== null) {
                $placedTeams[$position] = $teamKey;
            }

            $available = array_values(array_diff($available, [$position]));
        }

        foreach ($assigned as $position => $player) {
            $playList[$position] = $player;
        }

        return $this->improveSeparation($playList, $freePositions, $maxRound);
    }

    /**
     * 某位置放 teamKey 選手時，與已放置的同隊選手產生的成本。
     */
    private function positionCost(array $placedTeams, int $position, ?string $teamKey, int $maxRound): int
    {
        if ($teamKey === null) {
            return 0;
        }

        $cost = 0;

        foreach ($placedTeams as $placedPosition => $placedTeamKey) {
            $cost += $this->pairCost($placedTeamKey, $placedPosition, $teamKey, $position, $maxRound);
        }

        return $cost;
    }

    /**
     * 交換兩個非種子位置後的成本變化（只計算與這兩個位置相關的配對）。
     */
    private function swapDelta(array $teamsByPosition, int $a, int $b, int $maxRound): int
    {
        $delta = 0;

        foreach ($teamsByPosition as $position => $teamKey) {
            if ($position === $a || $position === $b) {
                continue;
            }

            $delta += $this->pairCost($teamKey, $position, $teamsByPosition[$a], $b, $maxRound)
                - $this->pairCost($teamKey, $position, $teamsByPosition[$a], $a, $maxRound)
                + $this->pairCost($teamKey, $position, $teamsByPosition[$b], $a, $maxRound)
                - $this->pairCost($teamKey, $position, $teamsByPosition[$b], $b, $maxRound);
        }

        return $delta;
    }

    /**
     * 隨機交換兩個非種子位置，總成本下降就保留（區域改進）。
     */
    private function improveSeparation(array $playList, array $positions, int $maxRound): array
    {
        $teamsByPosition = $this->teamsByPosition($playList);
        $attempts = max(100, min(count($positions) * 10, 600));

        for ($i = 0; $i < $attempts; $i++) {
            $a = $positions[array_rand($positions)];
            $b = $positions[array_rand($positions)];

            if ($a === $b || $this->swapDelta($teamsByPosition, $a, $b, $maxRound) >= 0) {
                continue;
            }

            [$playList[$a], $playList[$b]] = [$playList[$b], $playList[$a]];
            [$teamsByPosition[$a], $teamsByPosition[$b]] = [$teamsByPosition[$b], $teamsByPosition[$a]];
        }

        return $playList;
    }

    /**
     * @param  array  $playList  [position => player|null]
     * @return array [position => teamKey|null]
     */
    private function teamsByPosition(array $playList): array
    {
        $teams = [];

        foreach ($playList as $position => $player) {
            if (! is_null($player)) {
                $teams[$position] = $this->teamKey($player);
            }
        }

        return $teams;
    }

    /**
     * 同隊兩人在 bracket 相遇時的成本。
     */
    private function pairCost(?string $teamKeyA, int $positionA, ?string $teamKeyB, int $positionB, int $maxRound): int
    {
        if ($teamKeyA === null || $teamKeyA !== $teamKeyB || $positionA === $positionB) {
            return 0;
        }

        return 1 << ($maxRound - $this->meetingRound($positionA, $positionB));
    }

    /**
     * 兩個 bracket 位置（0-based，即 seat - 1）最早會在第幾回合相遇。
     * 位置差異的最高位 bit 長度就是相遇回合：差 1 → 第 1 回合、差 2~3 → 第 2 回合、差 4~7 → 第 3 回合。
     */
    private function meetingRound(int $a, int $b): int
    {
        return $a === $b ? 0 : strlen(decbin($a ^ $b));
    }

    private function maxRound(): int
    {
        return (int) round(log($this->getChartSize(), 2));
    }

    /**
     * 選手所屬隊伍（用來判斷同組織）；沒有隊伍的回傳 null（不參與分散）。
     */
    private function teamKey(array $player): ?string
    {
        $teamId = $player['athlete']['team']['id'] ?? null;

        return $teamId === null ? null : (string) $teamId;
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
