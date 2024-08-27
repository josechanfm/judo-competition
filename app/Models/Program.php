<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\BoutGenerationService;
use App\Services\DrawService;

class Program extends Model
{
    use HasFactory;

    public const ERM = 'erm';
    public const RRB = 'rrb';
    public const KOS = 'kos';
    public const RRBA = 'rrba';

    public const ALL_CONTEST_SYSTEMS = [
        self::ERM,
        self::RRB,
        self::KOS,
        self::RRBA,
    ];

    public const STATUS_CREATED = 0;
    public const STATUS_DREW = 1;
    public const STATUS_DREW_CONFIRMED = 2;
    public const STATUS_STARTED = 3;
    public const STATUS_FINISHED = 4;

    protected $attributes = [
        'status' => self::STATUS_CREATED,
    ];

    protected $fillable = ['competition_category_id', 'sequence', 'date', 'weight_code', 'mat', 'section', 'contest_system', 'chart_size', 'duration', 'status'];
    protected $appends = ['duration_formatted'];

    protected $with = [
        'competitionCategory'
    ];

    public function bouts()
    {
        return $this->hasMany(Bout::class);
    }

    public function category(){
        return $this->belongsTo(CompetitionCategory::class);
    }

    public function competition()
    {
        return $this->category->competition();
    }
    public function athletes()
    {
        return $this->belongsToMany(Athlete::class,'program_athlete')->withPivot(['seed','seat','weight','is_weight_passed','rank','score','confirm']);
    }

    public function programsAthletes()
    {
        return $this->hasMany(ProgramAthlete::class);
    }

    public function competitionCategory()
    {
        return $this->belongsTo(CompetitionCategory::class);
    }

    private function _roundToBaseTwoCeil($number): int
    {
        if ($number == 0) {
            return 1;
        } else {
            return pow(2, ceil(log($number, 2)));
        }
    }

    public function getAthletesAttribute()
    {
        return $this->athletes()->get();
    }

    public function getDurationFormattedAttribute()
    {
        return sprintf('%02d:%02d', floor($this->duration / 60), $this->duration % 60);
    }

    public function updateChartSize()
    {
        $athletesCount = $this->athletes()->count();

        $chartSize = pow(2, strlen(decbin($athletesCount - 1)));

        switch ($this->contest_system) {
            case self::ERM:
            case self::KOS:
                $this->chart_size = $chartSize;
                break;
            case self::RRB:
            case self::RRBA:
                $this->chart_size = $athletesCount;
                break;
        }
    }
    public function setProgram()
    {
        $athletesCount = $this->athletes()->count();

        // convert to rrb if athlete count < 8
        if ($athletesCount < 6) {
            $this->contest_system = self::RRB;
        }

        if ($athletesCount > 32) {
            $this->contest_system = self::KOS;
        }

        $this->updateChartSize();
        $this->save();
    }

    public function draw(): array
    {
        $athletes = (new DrawService($this))->draw();

        foreach ($athletes as $athlete) {
            $this->programsAthletes()->where('id', $athlete['id'])->update([
                'seat' => $athlete['seat'],
            ]);
        }
        // dd($this->competitionCategory->competition);
        (new BoutGenerationService($this->competitionCategory->competition))->assignAthletesToBouts($this);
        $this->status = Program::STATUS_DREW;
        $this->save();
        return $athletes;
    }
    public function confirmDraw()
    {
        $this->status = self::STATUS_DREW_CONFIRMED;
        $this->save();
    }
}
