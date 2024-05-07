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
     * @var Contest $contest
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private Competition $contest;

    public function startRow(): int
    {
        return 3;
    }

    public function headingRow(): int
    {
        return 2;
    }
    public function __construct(Competition $contest)
    {
        $this->contest = $contest;
    }

    public function collection(Collection $rows): void
    {
        // dd($rows);
        foreach ($rows as $index => $row) {
            $categories = $this->contest->openedCategories()->pluck('id', 'code');

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
                        $weightCodeNotExist = $this->contest->programs()
                            ->where('weight_code', $value)
                            ->where('category_id', $categories[$row['category']])
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
            $categoryId = $this->contest->openedCategories->where('code', $row['category'])->first()->id ?? null;

            $program = Program::where('contest_id', $this->contest->id)
                ->where('category_id', $categoryId)
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
            $this->enrollToProgram($program, $athlete, $row);
        }
    }
    private function createTeam($row): Team
    {
        // 創建代表隊資料
        return Team::firstOrCreate([
            'contest_id' => $this->contest->id,
            'abbreviation' => $row['team'],
        ]);
    }

    private function addAthleteToTeam(Team $team, $row): Athlete
    {
        return Athlete::firstOrCreate([
            'gender' => $row['gender'],
            'name' => $row['name'],
            'name_secondary' => $row['name_secondary'],
            'contest_id' => $this->contest->id,
            'team_id' => $team->id,
        ]);
    }

    private function enrollToProgram(Program $program, Athlete $athlete, $row)
    {
        $program->athletes()->attach($athlete->id, [
            'contest_id' => $this->contest->id,
            'seed' => $row['seed']
        ]);
    }
}
