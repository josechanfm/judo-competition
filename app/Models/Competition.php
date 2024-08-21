<?php

namespace App\Models;

use App\Services\BoutGenerationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Competition extends Model implements HasMedia
{
    use InteractsWithMedia;

    use HasFactory;
    protected $fillable = ['competition_type_id', 'type_id', 'date_start', 'date_end', 'country', 'name', 'name_secondary', 'scale', 'days', 'remark', 'mat_number', 'section_number', 'language', 'is_language_secondary_enabled', 'system', 'small_system', 'type', 'gender', 'seeding', 'language_secondary', 'token', 'is_cancelled', 'status'];
    protected $casts = [
        'days' => 'json',
        'is_language_secondary_enabled' => 'boolean',
        'small_system' => 'json'
    ];
    public function programs()
    {
        return $this->hasManyThrough(Program::class, CompetitionCategory::class);
    }
    public function programsBouts()
    {
        return $this->hasManyThrough(Program::class, CompetitionCategory::class)->with('bouts');
    }
    public function bouts()
    {

        $programIds = $this->programs->pluck('id');
        return Bout::whereIn('program_id', $programIds);
    }

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }
    public function programsAthletes()
    {
        return $this->hasManyThrough(Program::class, CompetitionCategory::class)->with('athletes');
    }
    // public function programsAthletes()
    // {
    //     return $this->hasMany(ProgramAthlete::class);
    // }
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
    public function getDrawBackgroundUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('draw-background') == '' ? asset('assets/draw-background.jpg') : $this->getFirstMediaUrl('draw-background');
    }

    public function getDrawCoverUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('draw-cover') == '' ? asset('assets/draw-background.jpg') : $this->getFirstMediaUrl('draw-cover');
    }
    public function referees()
    {
        return $this->hasMany(Referee::class);
    }
}
