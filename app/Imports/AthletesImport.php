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

    public function collection(Collection $rows): void
    {
        // dd($rows);
        foreach ($rows as $index => $row) {
            $count = count(array_filter($row->take(8)->toArray(), function ($value) {
                return $value !== null;
            }));
            if ($count == 0) {
                continue;
            }
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
                'team_abbreviation' => 'nullable|string',
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


            //                if ($program === null) {
            //                    dd($row);
            //                }
            // dd($row);
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
            'abbreviation' => $row['team_name'],
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
        $program->athletes()->attach($athlete->id, [
            'program_id' => $program->id,
            'athlete_id' => $athlete->id,
            'seed' => $row['seed'],
        ]);
    }
}
