<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $fillable = ['competition_id', 'name', 'name_secondary','name_display', 'gender', 'team_id', 'member_id'];
    use HasFactory;

    protected $with = ['team', 'programs'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function programAthletes()
    {
        return $this->hasMany(ProgramAthlete::class);
    }

    public function programs()
    {
        // 依「組別 → 公斤級」排序，讓列表的項目欄、編輯視窗已選標籤、
        // 以及各種 PDF 匯出的項目順序都一致（不是依 pivot 寫入順序）。
        return $this->belongsToMany(Program::class, 'program_athlete', 'athlete_id', 'program_id')
            ->orderByCategoryAndWeightGroup();
    }
}
