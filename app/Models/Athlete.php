<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $fillable = [
        'name_zh', 'name_pt', 'name_display', 'gender', 'team_id', 'competition_id'
    ];
    use HasFactory;

    public function programs()
    {
        return $this->belongsToMany(Program::class)->withPivot('id as athlete_program_id');
    }
}
