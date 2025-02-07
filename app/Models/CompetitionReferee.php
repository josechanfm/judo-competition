<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionReferee extends Model
{
    use HasFactory;

    protected $table = 'competition_referee';

    protected $fillable = ['competition_id', 'referee_id', 'mat_number', 'serial_number'];

    public function referee()
    {
        return $this->belongsTo(Referee::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
