<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Bout extends Model
{
    use HasFactory;
    protected $fillable = [
        'program_id',
        'sequence',
        'in_program_sequence',
        'queue',
        'date',
        'mat',
        'section',
        'competition_system',
        'round',
        'turn',
        'white',
        'blue',
        'white_rise_from',
        'blue_rise_from',
        'winner_rise_to',
        'loser_rise_to',
        'winner',
        'white_score',
        'blue_score',
        'duration',
        'status'
    ];

    protected $appends = ['white_player', 'blue_player', 'rank', 'status_text', 'duration_formatted', 'time_formatted', 'bout_name'];
    protected $cast = ['competition_referee_ids' => 'json'];

    public const STATUS_CANCELLED = -1;
    public const STATUS_PENDING = 0;
    public const STATUS_STARTED = 1;
    public const STATUS_FINISHED = 2;

    public function getWhitePlayerAttribute()
    {
        if ($this->white == 0) {
            //return Athlete::make(["name_display"=>"ss"]);
            return (object)["name_display" => ""];
        } else {
            return  ProgramAthlete::find($this->white)->athlete;
        };
    }
    public function getBluePlayerAttribute()
    {
        if ($this->blue == 0) {
            return (object)["name_display" => ""];
        } else {
            return  ProgramAthlete::find($this->blue)->athlete;
        };
    }
    public function getRankAttribute()
    {
        return $this->getRank();
    }

    public function getBoutNameAttribute()
    {
        try {
            switch ($this->competition_system) {
                case Program::KOS:
                    return $this->getKOSBoutName();
                case Program::RRB:
                    return $this->getRRBBoutName();
                case Program::ERM:
                    return $this->getERMBoutName();
                case Program::RRBA:
                    return $this->getRRBABoutName();
            }
        } catch (\Exception $e) {
            return '';
        }
    }


    public function getKOSBoutName(): string|null
    {
        if (!$this->round)
            return null;
        return match ($this->round) {
            64 => '64强',
            32 => '32强',
            16 => '16强',
            8 => '8强',
            4 => '4强',
            1 => '決賽',
        };
    }

    public function getERMBoutName(): string|null
    {
        if (!$this->round)
            return null;
        return match ($this->round) {
            64 => '64强',
            32 => '32强',
            16 => '16强',
            8 => '8强',
            4 => '4强',
            3 => '復活戰',
            2 => '季軍戰',
            1 => '決賽',
        };
    }

    public function getRRBBoutName(): string
    {
        return "循環賽(" . $this->in_program_sequence . "/" . $this->program->bouts()->count() . ")";
    }

    public function getRRBABoutName(): string|null
    {
        switch ($this->in_program_sequence) {
            case 1:
                return '循環賽(Y1)';
            case 2:
                return '循環賽(G)';
            case 3:
                return '循環賽(Y2)';
            case 4:
                return '循環賽(Y3)';
            case 5:
                return '循環賽(復活)';
            case 6:
                return '循環賽(決賽)';
        }

        return null;
    }

    public function getDurationFormattedAttribute(): string
    {
        return sprintf('%02d:%02d', floor($this->duration / 60), $this->duration % 60);
    }

    public function getTimeFormattedAttribute(): string
    {
        return sprintf('%02d:%02d', floor($this->time / 60), $this->time % 60);
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_PENDING => '未開始',
            self::STATUS_STARTED => '進行中',
            self::STATUS_FINISHED => '已完成',
            default => '未知',
        };
    }
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function whiteAthlete()
    {
        return $this->belongsTo(ProgramAthlete::class, 'white', 'id');
    }

    public function blueAthlete()
    {
        return $this->belongsTo(ProgramAthlete::class, 'blue', 'id');
    }
    public function result()
    {
        return $this->hasOne(BoutResult::class, 'bout_id', 'id');
    }
    public function winnerRiseTo(): Bout
    {
        return $this->belongsTo(Bout::class, 'winner_rise_to', 'in_program_sequence')
            ->where('program_id', $this->program_id)
            ->first();
    }

    public function loserRiseTo(): Bout
    {
        return $this->belongsTo(Bout::class, 'loser_rise_to', 'in_program_sequence')
            ->where('program_id', $this->program_id)
            ->first();
    }
    public static function fromProgram(Program $program): self
    {
        $bout = new self();
        $bout->program_id = $program->id;

        $bout->date = $program->date;
        $bout->mat = $program->mat;
        $bout->section = $program->section;
        $bout->competition_system = $program->competition_system;
        $bout->duration = $program->duration;

        Log::debug('Bout::fromProgram', [
            'program' => $program,
            'bout' => $bout,
        ]);

        return $bout;
    }
    static function blueRiseFrom($bout)
    {
        $riseBout = DB::table('bouts')->where('program_id', $bout->program_id)
            ->where('in_program_sequence', abs($bout->blue_rise_from))->first();
        if ($riseBout->status === -1 && $riseBout->winner === 0) {
            if ($riseBout->white === -1) {
                $riseBout = DB::table('bouts')->where('program_id', $riseBout->program_id)
                    ->where('in_program_sequence', abs($riseBout->blue_rise_from))->first();
            } else if ($riseBout->blue === -1) {
                $riseBout = DB::table('bouts')->where('program_id', $riseBout->program_id)
                    ->where('in_program_sequence', abs($riseBout->white_rise_from))->first();
            }
        }
        return $riseBout;
    }
    static function winOrLose($bout, $color)
    {
        $riseBout = DB::table('bouts')->where('program_id', $bout->program_id)
            ->where('in_program_sequence', abs($bout->{$color . '_rise_from'}))->first();
        if ($riseBout->status === -1 && $riseBout->winner === 0) {
            return '負方';
        } else {
            return $bout->{$color . '_rise_from'} > 0 ? '勝方' : '負方';
        }
    }

    static function whiteRiseFrom($bout)
    {
        $riseBout = DB::table('bouts')->where('program_id', $bout->program_id)
            ->where('in_program_sequence', abs($bout->white_rise_from))->first();
        if ($riseBout->status === -1 && $riseBout->winner === 0) {
            if ($riseBout->white === -1) {
                $riseBout = DB::table('bouts')->where('program_id', $riseBout->program_id)
                    ->where('in_program_sequence', abs($riseBout->blue_rise_from))->first();
            } else if ($riseBout->blue === -1) {
                $riseBout = DB::table('bouts')->where('program_id', $riseBout->program_id)
                    ->where('in_program_sequence', abs($riseBout->white_rise_from))->first();
            }
        }
        return $riseBout;
    }

    public function getWinner(): ProgramAthlete|null
    {
        if ($this->winner === $this->blue) {
            return $this->blueAthlete;
        }

        return $this->whiteAthlete->athlete;
    }

    public function getLoser(): ProgramAthlete|null
    {
        if ($this->winner === $this->blue) {
            return $this->whiteAthlete->athlete;
        }

        return $this->blueAthlete;
    }

    public function getLoserId(): int
    {
        if ($this->winner === $this->blue) {
            return $this->white;
        }

        return $this->blue;
    }

    public function getWinnerId(): int
    {
        return $this->winner;
    }

    public function isWhiteBye(): bool
    {
        return $this->white === -1;
    }

    public function isBlueBye(): bool
    {
        return $this->blue === -1;
    }

    /**
     * 處理輪空場次
     *
     * @return void
     */
    public function byeRise($stauts): void
    {
        if ($this->isWhiteBye()) {
            $this->setResult('blue');
        } else if ($this->isBlueBye()) {
            $this->setResult('white');
        }

        $this->invalidate($stauts);
        $this->save();
    }

    public function cancel($winner = -1)
    {
        $this->status = self::STATUS_CANCELLED;
        $this->winner = $winner;
        $this->rise();
        $this->save();
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function getScore(ProgramAthlete $programAthlete): int
    {
        if (!$this->result()->exists()) {
            return 0;
        }

        if ($this->white === $programAthlete->id) {
            return $this->result->w_score;
        } else if ($this->blue === $programAthlete->id) {
            return $this->result->b_score;
        }

        return 0;
    }

    public function isWinner(ProgramAthlete $programAthlete): bool
    {
        return $this->winner === $programAthlete->id;
    }

    private function setRankForKOSERM()
    {
        $rank = $this->getRank();

        $this->getWinner()?->setRank($rank);
        $this->getLoser()?->setRank($rank + 1);
    }

    private function setRankForRRB($winColor)
    {
        $athletes = $this->program->programsAthletes;
        $athletes->each(function (ProgramAthlete $programAthlete) {
            $programAthlete->collectScore();
        });
        $maxRank = $athletes->count();
        $athletes = $athletes->sortByDesc('score')->values();

        $athletes->each(function (ProgramAthlete $programAthlete, $index) use ($athletes, &$maxRank) {
            $rank = $athletes->count();
            for ($i = 0; $i < $maxRank; $i++) {
                if ($index != $i) {
                    if ($athletes[$i]->score === $programAthlete->score) {
                        if ($athletes[$i]->collectMark() === $programAthlete->collectMark()) {
                            if ($programAthlete->RankWinner($athletes[$i]->id)) {
                                $rank--;
                            }
                        } else if ($athletes[$i]->collectMark() < $programAthlete->collectMark()) {
                            $rank--;
                        }
                    } else if ($athletes[$i]->score < $programAthlete->score) {
                        $rank--;
                    }
                }
            }
            if ($athletes->count() == 5) {
                $rank = intval($rank);
                $self_ranks = [4 => 3];
                $rank = $self_ranks[$rank] ?? $rank;
            }
            $programAthlete->setRank($rank);
        });

        // $athletes = $athletes->sortByDesc('rank')->values();
        // $athletes->each(function (AthleteProgram $programAthlete, $index) use ($athletes, &$maxRank) {
        //     $rank = $programAthlete->rank + 1;
        //     for ($i = 0; $i < $maxRank; $i++) {
        //         if ($index != $i) {
        //             if ($athletes[$i]->rank === $programAthlete->rank) {
        //                 if ($athletes[$i]->collectTime() > $programAthlete->collectTime()) {
        //                     $rank--;
        //                 }
        //             }
        //         }
        //     }
        //     $programAthlete->setRank($rank);
        // });
    }

    private function setRankForRRBA($winColor)
    {
        if ($this->in_program_sequence === 6) {
            $this->getWinner()?->setRank(1);
            $this->getLoser()?->setRank(2);
        }

        if ($this->in_program_sequence === 5) {
            $this->program->programsAthletes()->where('rank', 3)->update(['rank' => 5]);
            $this->getWinner()?->setRank(3);
            $this->getLoser()?->setRank(4);
        }
    }

    public function setResult(string $winColor, BoutResult $boutResult = null)
    {
        $this->winner = $this->{$winColor};

        if ($boutResult) {
            $this->result()->delete();
            $this->result()->save($boutResult);
        }
        $this->save();
        switch ($this->competition_system) {
            case Program::KOS:
            case Program::ERM:
                $this->setRankForKOSERM();
                break;
            case Program::RRB:
                $this->setRankForRRB($winColor);
                break;
            case Program::RRBA:
                if ($this->in_program_sequence < 5) {
                    $this->setRankForRRB($winColor);
                } else {
                    $this->setRankForRRBA($winColor);
                }
                break;
        }

        $this->rise();
    }

    private function isFirstRoundBout(): bool
    {
        if ($this->in_program_sequence === 1)
            return true;

        $firstBout = $this->program
            ->bouts()
            ->where('status', self::STATUS_PENDING)
            ->orderBy('round', 'desc')
            ->pluck('round');

        return $firstBout->first() === $this->round;
    }

    private function isLastRoundBout(): bool
    {
        return $this->in_program_sequence === $this->program->bouts()->count();
    }

    public function touchProgramStatus()
    {
        if ($this->isLastRoundBout() || $this->program->bouts()->where('status', '!=', '-1')->where('status', '!=', '2')->get()->count() == 0) {
            $this->program->status = Program::STATUS_FINISHED;
            $this->program->save();
        } else if ($this->isFirstRoundBout()) {
            $this->program->status = Program::STATUS_STARTED;
            $this->program->save();
        }
    }

    /**
     * 將 bouts 設爲無效
     * @return void
     */
    private function invalidate($status): void
    {
        if ($status == 0) {
            $this->queue = 0;
        }
        $this->status = self::STATUS_CANCELLED;
        $this->save();
        $this->touchProgramStatus();
    }

    /**
     * 處理下一場次
     *
     * @return void
     */
    public function rise()
    {
        if ($this->competition_system === Program::RRBA) {
            $this->handleRRBARise();
        }
        // 如果有下一場
        if ($this->winner_rise_to !== 0) {
            $winnerTo = $this->winnerRiseTo();

            $toColor = ($winnerTo->white_rise_from == $this->in_program_sequence) ? 'white' : 'blue';
            $winnerTo->{$toColor} = $this->getWinnerId();
            $winnerTo->save();
        }

        // 如果有下一場
        if ($this->loser_rise_to !== 0) {
            $loserTo = $this->loserRiseTo();

            $toColor = $loserTo->white_rise_from == (0 - $this->in_program_sequence) ? 'white' : 'blue';

            // 如果 Loser 過磅失敗
            $loserTo->{$toColor} = $this->getLoser()?->is_weight_passed === 1 ? $this->getLoserId() : -1;
            $loserTo->save();
        }
    }

    /**
     * Handle special case for RRBA Rise
     *
     * @return void
     */
    private function handleRRBARise()
    {
        if ($this->in_program_sequence === 4) {
            // get total score for player 1,3,5
            $groupYellow = $this->program->programsAthletes()->whereIn('seat', [1, 3, 5])->orderBy('score', 'desc')->get();
            $yellow1st = $groupYellow->first();
            $yellow2nd = $groupYellow->skip(1)->first();

            $this->program->bouts()->where('in_program_sequence', 6)->update([
                'white' => $yellow1st->id,
            ]);

            $this->program->bouts()->where('in_program_sequence', 5)->update([
                'white' => $yellow2nd->id,
            ]);
        } else if ($this->in_program_sequence === 2) {
            // get total score for player 2,4
            $groupGreen = $this->program->programsAthletes()->whereIn('seat', [2, 4])->orderBy('score', 'desc')->get();
            $green1st = $groupGreen->first();
            $green2nd = $groupGreen->skip(1)->first();

            $this->program->bouts()->where('in_program_sequence', 6)->update([
                'blue' => $green1st->id,
            ]);

            $this->program->bouts()->where('in_program_sequence', 5)->update([
                'blue' => $green2nd->id,
            ]);
        }
    }

    public function scopeValid($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function start()
    {
        $this->status = self::STATUS_STARTED;
        $this->save();
    }

    public function reset()
    {
        $this->status = self::STATUS_PENDING;
        $this->save();
    }

    public function finish(string $color, BoutResult $boutResult)
    {
        $this->status = self::STATUS_FINISHED;
        $this->setResult($color, $boutResult);
        // $this->save();
    }

    public function uploadResult(int $result, BoutResult $boutResult)
    {
        switch ($result) {
            case BoutResult::STATUS_CANCELLED:
                $this->cancel();
                break;
            case BoutResult::STATUS_WHITE_WIN:
            case BoutResult::STATUS_BLUE_ABSTAIN:
            case BoutResult::STATUS_BLUE_HANSOKUMAKE:
            case BoutResult::STATUS_BLUE_MEDICAL:
                $this->finish('white', $boutResult);
                break;
            case BoutResult::STATUS_BLUE_WIN:
            case BoutResult::STATUS_WHITE_ABSTAIN:
            case BoutResult::STATUS_WHITE_HANSOKUMAKE:
            case BoutResult::STATUS_WHITE_MEDICAL:
                $this->finish('blue', $boutResult);
                break;
        }

        $this->touchProgramStatus();
    }

    public function getERMRank(): int
    {
        return $this->program
            ->bouts()
            ->pluck('round')
            ->unique()
            ->values()
            ->sort()
            ->values()
            ->search($this->round) * 2 + 1;
    }

    public function getKOSRank(): int
    {
        return $this->program
            ->bouts()
            ->pluck('round')
            ->unique()
            ->values()
            ->sort()
            ->values()
            ->search($this->round) + 1;
    }

    public function getRank(): int
    {
        return ($this->competition_system === Program::ERM) ? 2 : 3;
    }

    public function updateScore($whiteScore, $blueScore)
    {
        $this->white_score = $whiteScore;
        $this->blue_score = $blueScore;
        $this->save();
    }

    public function whiteRiseFromQueue($bout): int
    {
        return $this->whiteRiseFrom($bout)->queue;
    }

    public function blueRiseFromQueue($bout): int
    {
        return $this->blueRiseFrom($bout)->queue;
    }

    public function referees()
    {
        return CompetitionReferee::whereIn('id', $this->competition_referee_ids)->with('referee')->get();
    }
}
