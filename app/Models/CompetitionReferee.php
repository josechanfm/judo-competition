<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionReferee extends Model
{
    use HasFactory;
    
    protected $table='CompetitionReferee';

    public function referee(){
        return $this->belongsTo(Referee::class);
    }
}


