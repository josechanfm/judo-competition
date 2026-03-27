<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramAthlete extends Model
{
    protected $fillable = [
        'program_id',
        'athlete_id',
        'seed',
        'seat',
        'weight',
        'signature',
        'is_weight_passed',
        'rank',
        'score',
        'confirm'
    ];
    use HasFactory;

    protected $table = 'program_athlete';

    protected $with = ['athlete'];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function category()
    {
        return $this->hasOneThrough(
            CompetitionCategory::class,
            Program::class,
            'id', 
            'id',
            'program_id', 
            'competition_category_id' 
        );
    }
    public function collectScore()
    {
        $this->score = 0;

        // $this->program->bouts()->where('white', $this->id)
        //                        ->orWhere('blue', $this->id)
        //                        ->get()
        //                        ->each(function (Bout $bout) {
        //     $this->score += $bout->isWinner($this) ? 1 : 0;
        // });

        $this->score = $this->program->bouts()->where('winner', $this->id)->count();

        $this->save();
    }
    public function competition()
    {
        return $this->hasOneThrough(
            Competition::class,
            Program::class,
            'id', 
            'id', 
            'program_id', 
            'competition_id'
        );
    }

    public function team()
    {
        return $this->hasOneThrough(Team::class, Athlete::class, 'id', 'id', 'athlete_id', 'team_id');
    }
    public function setRank(int $rank)
    {
        $this->rank = $rank;
        $this->save();
    }
    public function collectMark()
    {
        $mark = 0;

        $bouts = $this->program->bouts()->where('winner', $this->id)->get();
        foreach ($bouts as $b) {
            $result = $b->result()->first();
            $mark = $mark + max($result->w_score ?? 0 , $result->b_score ?? 0);
        }
        return $mark;
    }
    public function RankWinner($otherAthlete)
    {
        $bout = $this->program->bouts()->where(function ($bout) {
            $bout->where('white', '=', $this->id)
                ->orWhere('blue', '=', $this->id);
        })->where(function ($bout) use ($otherAthlete) {
            $bout->where('white', '=', $otherAthlete)
                ->orWhere('blue', '=', $otherAthlete);
        })->where('winner', $this->id)->first();

        return $bout;
    }

    public function battleBout($otherAthlete)
    {
        return Bout::where(function ($query) use ($otherAthlete) {
            // 情況1: 當前運動員是 blue，另一個是 white
            $query->where('blue', $this->id)
                ->where('white', $otherAthlete->id);
        })->orWhere(function ($query) use ($otherAthlete) {
            // 情況2: 當前運動員是 white，另一個是 blue
            $query->where('white', $this->id)
                ->where('blue', $otherAthlete->id);
        })->first(); // 或使用 ->get() 如果可能有多場比賽
    }
}
