<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkedBout extends Bout
{
    protected $table = 'bouts';

    protected $appends = ['prev_white', 'prev_blue', 'is_bout'];

    public function getIsBoutAttribute(): bool
    {
        return true;
    }

    public function getPrevWhiteAttribute(): Model|\stdClass
    {
        return $this->belongsTo(LinkedBout::class, 'white_rise_from', 'in_program_sequence')
            ->where('program_id', $this->program_id)
            ->first() ?? $this->getWhitePlayer();
    }

    public function getPrevBlueAttribute(): Model|\stdClass
    {
        return $this->belongsTo(LinkedBout::class, 'blue_rise_from', 'in_program_sequence')
            ->where('program_id', $this->program_id)
            ->first() ?? $this->getBluePlayer();
    }

    private function _getMaxDepth(mixed $bout): int
    {
        if (!($bout instanceof LinkedBout)) {
            return 0;
        } else {
            $wDepth = $this->_getMaxDepth($bout->getPrevWhiteAttribute());
            $bDepth = $this->_getMaxDepth($bout->getPrevBlueAttribute());

            if ($wDepth > $bDepth) {
                return $wDepth + 1;
            } else {
                return $bDepth + 1;
            }
        }
    }

    public function getMaxDepth(): int
    {
        return $this->_getMaxDepth($this);
    }

    public function getPlayer($color): Model|null
    {
        return $this->belongsTo(ProgramAthlete::class, $color, 'id')
            ->where('program_id', $this->program_id)
            ->with(['athlete', 'team'])
            ->first();
    }

    public function getWhitePlayer(): object
    {
        return ((object) [
            'program_athlete' => $this->getPlayer('white'),
            'raw_value' => $this->white,
            'color' => 'white',
        ]);
    }

    public function getBluePlayer(): object
    {
        return ((object) [
            'program_athlete' => $this->getPlayer('blue'),
            'raw_value' => $this->blue,
            'color' => 'blue'
        ]);
    }

    public function getKosPools()
    {
        return [
            [
                'name' => 'Pool A',
                'color' => 'bg-blue-400',
            ],
            [
                'name' => 'Pool B',
                'color' => 'bg-green-400',
            ],
            [
                'name' => 'Pool C',
                'color' => 'bg-pink-400',
            ],
            [
                'name' => 'Pool D',
                'color' => 'bg-yellow-400',
            ],
        ];
    }

    public function getPools(): array
    {
        switch ($this->program->contest_system) {
            case Program::KOS:
                if ($this->program->chart_size >= 8)
                    return $this->getKosPools();
            case Program::ERM:
                if ($this->program->chart_size >= 8)
                    if ($this->round === 1)
                        return $this->getKosPools();
                    else {
                        return [
                            [
                                'round' => $this->round,
                                'name' => 'Repechage',
                                'color' => 'bg-gray-400',
                            ]
                        ];
                    }
        }

        return [];
    }
}
