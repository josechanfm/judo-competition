<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramAthlete extends Model
{
    use HasFactory;

    protected $table = 'programs_athlete';

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
