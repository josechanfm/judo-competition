<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 'athlete_id'
    ];
    
    protected $table = 'athlete_program';
}
