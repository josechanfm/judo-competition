<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function bouts(){
        return $this->hasMany(Bout::class);
    }
    public function athletes(){
        return $this->belongsToMany(Athlete::class);
    }
}
