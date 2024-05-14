<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'name_zh', 'name_en', 'name_pt', 'abbreviation'
    ];

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }
}
