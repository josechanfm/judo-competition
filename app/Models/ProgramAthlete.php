<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramAthlete extends Model
{
    protected $fillable = [
        'program_id',
        'athlete_id'
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

    public function setRank(int $rank)
    {
        $this->rank = $rank;
        $this->save();
    }
}
