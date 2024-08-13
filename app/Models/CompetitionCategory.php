<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionCategory extends Model
{
    use HasFactory;
    protected $fillable = ['competition_id', 'code', 'name', 'name_secondary', 'duration', 'weights'];
    protected $casts = ['weights' => 'json'];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
    public function programs(){
        return $this->hasMany(Program::class);
    }
}
