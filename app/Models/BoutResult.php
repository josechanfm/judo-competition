<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoutResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'bout_id',
        'status',
        'w_ippon',
        'w_wazari',
        'w_yuko',
        'w_shido',
        'b_ippon',
        'b_wazari',
        'b_yuko',
        'b_shido',
        'w_score',
        'b_score',
        'time',
        'device_uuid',
        'actions',
    ];

    public const STATUS_CANCELLED = -1;

    // 白方勝利
    public const STATUS_WHITE_WIN = 10;

    // 藍方勝利
    public const STATUS_BLUE_WIN = 11;

    // 白方退賽，藍方勝利
    public const STATUS_WHITE_ABSTAIN = 20;
    public const STATUS_WHITE_MEDICAL = 30;
    public const STATUS_WHITE_HANSOKUMAKE = 40;

    // 藍方退賽，白方勝利
    public const STATUS_BLUE_ABSTAIN = 21;
    public const STATUS_BLUE_MEDICAL = 31;
    public const STATUS_BLUE_HANSOKUMAKE = 41;


    public function bout()
    {
        return $this->belongsTo(Bout::class);
    }
}
