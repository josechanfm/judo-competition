<?php

namespace App\Utils;

class WeightUtil
{
    private const REGEX = "/(M|F|MF)W((\d+)(\+|\-)|ULW|OPEN|0)/i";

    public static function weightCodeSegment($weight)
    {
        $matches = [];

        preg_match(self::REGEX, $weight, $matches);

        if (count($matches) === 5) {
            return [
                'gender' => $matches[1],
                'kilo' => $matches[3],
                'group' => $matches[4],
            ];
        }

        return [
            'gender' => $matches[1],
            'group' => $matches[2],
        ];
    }
    public static function sortWeights(array $weights): array
    {
        return collect($weights)->sortBy(function ($weight) {
            $segment = self::weightCodeSegment($weight);

            $priority = ((int) ($segment['gender'] === 'F')) * 10000;

            if (!isset($segment['kilo'])) {
                $priority += (999 + 2000);
            } else {
                $priority += $segment['kilo'] +
                    ((int)($segment['group'] === '+')) * 1000;
            }

            return $priority;
        })->values()->toArray();
    }
}
