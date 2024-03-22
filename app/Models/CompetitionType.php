<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionType extends Model
{
    use HasFactory;
    protected $fillable = ['competition_id','name', 'name_secondary', 'code', 'winner_plus', 'language', 'is_language_secondary_enabled', 'language_secondary'];
    protected $table = 'competition_type';
}
