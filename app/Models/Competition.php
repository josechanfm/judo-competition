<?php

namespace App\Models;

use App\Services\BoutGenerationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;
    protected $fillable = ['competition_type_id', 'type_id', 'date_start', 'date_end', 'country', 'name', 'name_secondary', 'scale', 'days', 'remark', 'mat_number', 'section_number', 'language', 'is_language_secondary_enabled', 'language_secondary', 'token', 'is_cancelled', 'status'];
    protected $casts = [
        'days' => 'array',
        'is_language_secondary_enabled' => 'boolean',
    ];
    public function programs()
    {
        return $this->hasMany(Program::class);
    }
    public function bouts()
    {
        return $this->hasManyThrough(Bout::class, Program::class)->orderBy('round', 'desc')->orderBy('sequence');
    }
    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
    public function categories()
    {
        return $this->hasMany(CompetitionCategory::class);
    }
    public function competition_type()
    {
        return $this->hasOne(CompetitionType::class);
    }
    public function generateBouts()
    {
        (new BoutGenerationService($this))->generate();
    }
}
