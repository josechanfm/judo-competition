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
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Validators\Failure;

class AthletesImport implements ToCollection, WithStartRow, SkipsOnFailure, WithHeadingRow
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

    public function collection(Collection $rows): void
    {
        // dd($rows);
        foreach ($rows as $index => $row) {
            $categories = $this->competition->categories()->pluck('id', 'code');

            $validator = Validator::make($row->toArray(), [
                'gender' => 'required|in:M,F',

                'category' => ['required', function ($attribute, $value, $fail) use (&$categories) {
                    if (!$categories->has($value)) {
                        $fail(trans('validation.invalid_category_code'));
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
                        $fail(trans('validation.invalid_weight_code'));
                    }
                }],
                'team' => 'required',
                'name' => 'nullable|string',
                'name_secondary' => 'nullable|string',
                'seed' => 'nullable|integer|min:0|max:32',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $attr => $messages) {
                    $this->failures[] = new Failure($index, $attr, $messages, $row->toArray());
                }

                continue;
            }
            $categoryId = $this->competition->categories->where('code', $row['category'])->first()->id ?? null;

            $program = Program::where('competition_id', $this->competition->id)
                ->where('competition_category_id', $categoryId)
                ->where('weight_code', $row['weight_code'])
                ->first();


            //                if ($program === null) {
            //                    dd($row);
            //                }

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
            'abbreviation' => $row['team'],
        ]);
    }

    private function addAthleteToTeam(Team $team, $row): Athlete
    {
        return Athlete::firstOrCreate([
            'gender' => $row['gender'],
            'name_zh' => $row['name'],
            'name_pt' => $row['name_secondary'],
            'name_display' => $row['name'],
            'competition_id' => $this->competition->id,
            'team_id' => $team->id,
        ]);
    }

    private function enrollToProgram(Program $program, Athlete $athlete, Team $team,  $row)
    {
        $program->athletes()->attach($athlete->id, [
            'competition_id' => $this->competition->id,
            'team_id' => $team->id,
            'program_id' => $program->id,
            'athlete_id' => $athlete->id,
        ]);
    }
}
