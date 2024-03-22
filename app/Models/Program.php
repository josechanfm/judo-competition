<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $fillable = ['competition_id', 'competition_category_id', 'sequence', 'date', 'weight_code', 'mat', 'section', 'contest_system', 'chart_size', 'duration', 'status'];
    public function bouts()
    {
        return $this->hasMany(Bout::class);
    }
    public function athletes()
    {
        return $this->belongsToMany(Athlete::class)->withPivot('id as athlete_program_id');
    }
}
