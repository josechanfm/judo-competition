<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Bout extends Model
{
    use HasFactory;
    protected $fillable = [
        'program_id', 'sequence', 'in_program_sequence', 'queue', 'date', 'mat', 'section', 'contest_system', 'round', 'turn', 'white', 'blue', 'white_rise_from', 'blue_rise_from', 'winner_rise_to', 'loser_rise_to', 'winner', 'white_score', 'blue_score', 'duration', 'status'
    ];

    protected $appends = ['white_player', 'blue_player'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function getWhitePlayerAttribute()
    {
        if ($this->white == 0) {
            //return Athlete::make(["name_display"=>"ss"]);
            return (object)["name_display" => ""];
        } else {
            return  Athlete::find($this->white);
        };
    }
    public function getBluePlayerAttribute()
    {
        if ($this->blue == 0) {
            return (object)["name_display" => ""];
        } else {
            return  Athlete::find($this->blue);
        };
    }

    public static function fromProgram(Program $program): self
    {
        $bout = new self();
        $bout->program_id = $program->id;

        $bout->date = $program->date;
        $bout->mat = $program->mat;
        $bout->section = $program->section;
        $bout->contest_system = $program->contest_system;
        $bout->duration = $program->duration;

        Log::debug('Bout::fromProgram', [
            'program' => $program,
            'bout' => $bout,
        ]);

        return $bout;
    }
}
