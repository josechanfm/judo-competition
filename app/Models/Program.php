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

    public const ALL_competition_systemS = [
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

    protected $fillable = ['competition_category_id', 'sequence', 'date', 'weight_code', 'mat', 'section', 'competition_system', 'chart_size', 'duration', 'status'];
    protected $appends = ['duration_formatted'];

    protected $with = [
        'competitionCategory'
    ];

    public function bouts()
    {
        return $this->hasMany(Bout::class)->orderBy('in_program_sequence');
    }

    public function competition()
    {
        return $this->competitionCategory->competition();
    }
    public function athletes()
    {
        return $this->belongsToMany(Athlete::class, 'program_athlete')->withPivot(['seed', 'seat', 'weight', 'is_weight_passed', 'rank', 'score', 'confirm']);
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

        switch ($this->competition_system) {
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
        if ($this->competition->system == 'Q') {
            $this->competition_system = self::ERM;
        }

        if ($athletesCount == 2) {
            $this->competition_system = self::KOS;
        }

        if ($athletesCount == 3) {
            if ($this->competition->small_system[3] == false) {
                $this->competition_system = self::RRB;
            } else {
                $this->competition_system = self::KOS;
            }
        }
        if ($athletesCount == 4) {
            if ($this->competition->small_system[4] == false) {
                $this->competition_system = self::RRB;
            } else {
                $this->competition_system = self::ERM;
            }
        }

        if ($athletesCount == 5) {
            if ($this->competition->small_system[5] == false) {
                $this->competition_system = self::RRB;
            } else {
                $this->competition_system = self::RRBA;
            }
        }
        // dd($this->competition_system);
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

    public function convertWeight()
    {
        $weight = $this->weight_code;
        if (preg_match('/^MW(\d+)([+-])$/', $weight, $matches)) {
            $sign = $matches[2];
            $value = $matches[1];

            if ($sign === '-') {
                return "-{$value}kg";
            } elseif ($sign === '+') {
                return "+{$value}kg";
            }
        }
        return $weight;
    }
}
