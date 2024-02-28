<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'name_secondary', 'code', 'winner_plus', 'language', 'is_language_secondary_enabled', 'language_secondary'];

    public function categories()
    {
        return $this->hasMany(GameCategory::class);
    }
}
