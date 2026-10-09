<?php

namespace App\Imports;

use App\Models\Competition;
use App\Models\ProgramAthlete;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

/**
 * 匯入過磅資料（體重 + 過磅結果）。
 *
 * 搭配 WeightInExcelExport 的欄位：選手ID / 組別 / 公斤級 / 參賽項目 / 隊伍 /
 * 姓名 / 外文姓名 / 種子 / 體重(kg) / 過磅結果。
 *
 * 比對順序：
 *   1. 「選手ID」＝ program_athlete.id（且必須屬於這場賽事）。
 *   2. 否則用「組別 + 公斤級 + 姓名」比對。
 *
 * 只有「體重(kg)」與「過磅結果」會被更新，其他欄位僅供辨識。
 */
class WeightInImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /** 成功更新的筆數 */
    public int $updated = 0;

    /** 逐列錯誤：['row' => int, 'message' => string] */
    public array $errors = [];

    /** @var array<string, ProgramAthlete> */
    private array $byId = [];

    /** @var array<string, ProgramAthlete> */
    private array $byKey = [];

    public function __construct(private Competition $competition)
    {
        // 保留原始中文標題，不用 Str::slug（中文會被清空）
        HeadingRowFormatter::default('none');

        $this->buildIndex();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // 標題在第 1 列，資料從第 2 列開始
            $rowNumber = $index + 2;

            $data = [];
            foreach ($row as $key => $value) {
                $data[trim((string) $key)] = $value;
            }

            $id = trim((string) ($data['選手ID'] ?? ''));
            $category = trim((string) ($data['組別'] ?? ''));
            $weightCode = trim((string) ($data['公斤級'] ?? ''));
            $name = trim((string) ($data['姓名'] ?? ''));
            $weight = $data['體重(kg)'] ?? null;
            $result = trim((string) ($data['過磅結果'] ?? ''));

            $programAthlete = $this->resolveProgramAthlete($id, $category, $weightCode, $name);

            if (! $programAthlete) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => '找不到對應的選手：' . ($name !== '' ? $name : "ID {$id}"),
                ];
                continue;
            }

            $changed = false;

            if ($weight !== null && trim((string) $weight) !== '') {
                if (! is_numeric($weight)) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => '體重不是數字：' . $weight,
                    ];
                    continue;
                }

                $programAthlete->weight = round((float) $weight, 2);
                $changed = true;
            }

            if ($result !== '') {
                $normalized = $this->normalizeResult($result);

                if ($normalized === 'invalid') {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => '無法辨識的過磅結果：' . $result . '（可用：通過 / 不通過 / 未過磅）',
                    ];
                    continue;
                }

                $programAthlete->is_weight_passed = $normalized;
                $changed = true;
            }

            if ($changed) {
                $programAthlete->save();
                $this->updated++;
            }
        }
    }

    private function resolveProgramAthlete(string $id, string $category, string $weightCode, string $name): ?ProgramAthlete
    {
        if ($id !== '' && isset($this->byId[$id])) {
            return $this->byId[$id];
        }

        return $this->byKey[$this->matchKey($category, $weightCode, $name)] ?? null;
    }

    private function buildIndex(): void
    {
        $programs = $this->competition->programs()
            ->with(['competitionCategory', 'programAthletes.athlete'])
            ->orderByCategoryAndWeightGroup()
            ->get();

        foreach ($programs as $program) {
            $categoryName = (string) ($program->competitionCategory->name ?? '');

            foreach ($program->programAthletes as $programAthlete) {
                $this->byId[(string) $programAthlete->id] = $programAthlete;

                $key = $this->matchKey(
                    $categoryName,
                    (string) $program->weight_code,
                    (string) ($programAthlete->athlete->name ?? '')
                );

                $this->byKey[$key] = $programAthlete;
            }
        }
    }

    private function matchKey(string $category, string $weightCode, string $name): string
    {
        return mb_strtolower(trim($category))
            . '|' . mb_strtolower(trim($weightCode))
            . '|' . trim($name);
    }

    /**
     * @return int|null|string 1 = 通過, 0 = 不通過, null = 未過磅, 'invalid' = 無法辨識
     */
    private function normalizeResult(string $value)
    {
        $normalized = mb_strtolower(trim($value));

        if (in_array($normalized, ['1', '通過', 'pass', 'passed', 'p', 'y', 'yes', '是'], true)) {
            return 1;
        }

        if (in_array($normalized, ['0', '不通過', 'fail', 'failed', 'f', 'n', 'no', '否'], true)) {
            return 0;
        }

        if (in_array($normalized, ['未過磅', '待過磅', 'pending', '-', '--'], true)) {
            return null;
        }

        return 'invalid';
    }
}
