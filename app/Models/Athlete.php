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
