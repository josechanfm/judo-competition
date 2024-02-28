<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameCategory extends Model
{
    use HasFactory;
    protected $fillable = ['game_type_id', 'name', 'name_secondary', 'code', 'weights', 'duration'];
    protected $casts = ['weights' => 'json'];

    public function setTimeDuration()
    {
        $this->druation = '04:00';
    }
}
