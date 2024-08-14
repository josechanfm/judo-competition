<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $fillable = ['competition_id', 'name', 'name_display', 'gender', 'team_id', 'member_id'];
    use HasFactory;
    protected $with = [
        'team',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
