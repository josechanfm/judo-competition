<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\Program;
use App\Models\Team;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Validators\Failure;

class AthletesImport implements ToCollection, WithStartRow, SkipsOnFailure, WithHeadingRow, SkipsEmptyRows
{
    use SkipsFailures, Importable;
    /**
     * @param array $row
     * @var Competition $competition
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private Competition $competition;

    public function startRow(): int
    {
        return 3;
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function normalizeGender($value): ?string
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $mapped = [
            'm' => 'M',
            'male' => 'M',
            '男' => 'M',
            '男性' => 'M',
            'f' => 'F',
            'female' => 'F',
            '女' => 'F',
            '女性' => 'F',
        ];

        $lower = strtolower($normalized);

        return $mapped[$lower] ?? strtoupper($normalized);
    }

    public function normalizeCategory($value): ?string
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $categories = $this->competition->categories ?? collect();
        $match = $categories->first(function ($category) use ($normalized) {
            $code = (string) ($category->code ?? '');
            $name = (string) ($category->name ?? '');
            $nameSecondary = (string) ($category->name_secondary ?? '');

            return strtolower($code) === strtolower($normalized)
                || strtolower($name) === strtolower($normalized)
                || strtolower($nameSecondary) === strtolower($normalized);
        });

        return $match ? (string) $match->code : $normalized;
    }

    public function normalizeWeightCode($value, ?string $gender = null): ?string
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $upper = strtoupper($normalized);

        if (in_array($upper, ['MWULW', 'FWULW'], true)) {
            return $upper;
        }

        if (in_array($upper, ['ULW', 'OPEN'], true)) {
            $genderCode = strtoupper((string) $gender);
            if (!in_array($genderCode, ['M', 'F'], true)) {
                $genderCode = 'F';
            }

            return $genderCode . 'WULW';
        }

        if (in_array($upper, ['MULW', 'FULW'], true)) {
            return $upper[0] . 'WULW';
        }

        if (preg_match('/^(?:M|F)?(\d+)([+-]?)$/', $upper, $matches)) {
            $weight = $matches[1];
            $suffix = $matches[2] ?? '';
            $genderCode = strtoupper((string) $gender);

            if (!in_array($genderCode, ['M', 'F'], true)) {
                $genderCode = 'F';
            }

            return $genderCode . 'W' . $weight . $suffix;
        }

        if (in_array($upper, ['MW', 'FW'], true)) {
            return $upper . 'ULW';
        }

        return $upper;
    }

    public function collection(Collection $rows): void
    {
        // 記錄每個項目(program)已加入的選手姓名，用來去除「相同姓名 + 相同項目」的重複報名
        $enrolledNames = [];

        // dd($rows);
        foreach ($rows as $index => $row) {
            $count = count(array_filter($row->take(8)->toArray(), function ($value) {
                return $value !== null;
            }));
            if ($count == 0) {
                continue;
            }
            $row = $row->mapWithKeys(function ($value, $key) use ($row) {
                if ($key === 'gender') {
                    return ['gender' => $this->normalizeGender($value)];
                }

                if ($key === 'category') {
                    return ['category' => $this->normalizeCategory($value)];
                }

                if ($key === 'weight_code') {
                    $gender = $this->normalizeGender($row->get('gender'));
                    return ['weight_code' => $this->normalizeWeightCode($value, $gender)];
                }

                return [$key => $value];
            });

            $categories = $this->competition->categories()->pluck('id', 'code');
            // dd($row);
            $validator = Validator::make($row->toArray(), [
                'gender' => 'required|in:M,F',

                'category' => ['required', function ($attribute, $value, $fail) use (&$categories) {
                    if (!$categories->has($value)) {
                        $fail('There is no category for this athlete.');
                    }
                }],
                'weight_code' => ['required', function ($attribute, $value, $fail) use ($row, &$categories) {
                    if (!$categories->has($row['category'])) {
                        $weightCodeNotExist = true;
                    } else {
                        $weightCodeNotExist = $this->competition->programs()
                            ->where('weight_code', $value)
                            ->where('competition_category_id', $categories[$row['category']])
                            ->doesntExist();
                    }

                    if ($weightCodeNotExist) {
                        $fail('There is no weight code for this athlete.');
                    }
                }],
                'team_name' => 'required',
                'team_code' => 'nullable|string',
                'name' => 'nullable|string',
                'name_secondary' => 'nullable|string',
                'seed' => 'nullable|integer|min:0|max:32',
            ]);
            // dd($validator->errors())->message();
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $attr => $messages) {
                    $this->failures[] = new Failure($index, $attr, $messages, $row->toArray());
                }

                continue;
            }
            $categoryId = $this->competition->categories->where('code', $row['category'])->first()->id ?? null;

            $program = Program::where('competition_category_id', $categoryId)
                ->where('weight_code', $row['weight_code'])
                ->first();

            // 去除重複報名：以「姓名 = name + name_secondary」+ 同一「項目(program)」去重，只保留第一筆
            $athleteName = trim((string)($row['name'] ?? '') . (string)($row['name_secondary'] ?? ''));
            if ($athleteName !== '' && isset($enrolledNames[$program->id][strtolower($athleteName)])) {
                continue; // 已有相同姓名報名同一項目，跳過以免重複
            }
            if ($athleteName !== '') {
                $enrolledNames[$program->id][strtolower($athleteName)] = true;
            }

            // 創建代表隊資料
            $team = $this->createTeam($row);
            // 創建選手資料
            $athlete = $this->addAthleteToTeam($team, $row);

            // 關聯項目與選手
            $this->enrollToProgram($program, $athlete, $team, $row);
        }
    }
    private function createTeam($row): Team
    {
        // 創建代表隊資料
        return Team::firstOrCreate([
            'competition_id' => $this->competition->id,
            'name' => $row['team_name'],
            'abbreviation' => $row['team_code'] ?? NULL,
        ]);
    }

    private function addAthleteToTeam(Team $team, $row): Athlete
    {
        return Athlete::firstOrCreate([
            'gender' => $row['gender'],
            'name' => $row['name'] ?? null,
            'name_secondary' => $row['name_secondary'] ?? null,
            'name_display' => $row['name'] ?? '' . $row['name_secondary'] ?? '',
            'competition_id' => $this->competition->id,
            'team_id' => $team->id,
        ]);
    }

    private function enrollToProgram(Program $program, Athlete $athlete, Team $team,  $row)
    {
        // 保險：若該選手已存在於同一項目中，不再重複加入
        if ($program->programAthletes()->where('athlete_id', $athlete->id)->exists()) {
            return;
        }

        $program->athletes()->attach($athlete->id, [
            'program_id' => $program->id,
            'athlete_id' => $athlete->id,
            'seed' => $row['seed'],
        ]);
    }
}
