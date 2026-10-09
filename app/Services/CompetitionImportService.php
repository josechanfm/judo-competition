<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * 還原 {@see CompetitionExportService} 產生的 ZIP 備份。
 *
 * 一律「建立一場全新的賽事」，不動到既有資料；所有被參考的 id
 * （組別／項目／隊伍／選手／報名／場次／裁判）都會重新對應到新建立的資料列。
 *
 * 幾點必須知道的關聯語意：
 * - `bouts.white` / `blue` / `winner` 存的是 **program_athlete.id** → 需要重對應。
 * - `bouts.white_rise_from` / `blue_rise_from` / `winner_rise_to` / `loser_rise_to`
 *   存的是 **in_program_sequence**（見 Bout::winnerRiseTo() 等關聯定義）→ 不需重對應，
 *   只要 in_program_sequence 原樣保留即可。
 * - `bouts.competition_referee_ids` 存的是 **competition_referee.id** 的 JSON 陣列 → 需要重對應。
 * - `referees` 是全域表（沒有 competition_id）→ 以 name + country 比對，找不到才新建。
 * - `competitions.token` 是公開查詢用的唯一鍵（Api\* 都用 where('token', ...) 找賽事），
 *   匯入時**一定要重新產生**，否則會和來源賽事撞號。
 */
class CompetitionImportService
{
    /**
     * 匯入 ZIP，回傳新建立的賽事。
     *
     * @throws \RuntimeException 備份檔格式錯誤或內容不完整時
     */
    public function import(string $zipPath): Competition
    {
        $extractDir = $this->extract($zipPath);

        try {
            $data = $this->readData($extractDir);

            $competition = DB::transaction(fn () => $this->restore($data, $extractDir));
        } finally {
            File::deleteDirectory($extractDir);
        }

        return $competition;
    }

    /**
     * 解壓到暫存目錄，並回傳該目錄。
     */
    private function extract(string $zipPath): string
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('無法開啟檔案，請確認是有效的 ZIP 壓縮檔。');
        }

        $extractDir = storage_path(
            'app/competition-transfer/' . uniqid('import-', true)
        );

        if (! is_dir($extractDir) && ! mkdir($extractDir, 0777, true) && ! is_dir($extractDir)) {
            $zip->close();
            throw new \RuntimeException('無法建立暫存目錄。');
        }

        $zip->extractTo($extractDir);
        $zip->close();

        return $extractDir;
    }

    private function readData(string $extractDir): array
    {
        $path = $extractDir . DIRECTORY_SEPARATOR . CompetitionExportService::DATA_FILE;

        if (! is_file($path)) {
            throw new \RuntimeException(
                '壓縮檔裡找不到 ' . CompetitionExportService::DATA_FILE . '，這不是本系統匯出的賽事備份檔。'
            );
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data) || ($data['format'] ?? null) !== CompetitionExportService::FORMAT) {
            throw new \RuntimeException('備份檔格式不正確（format 不符）。');
        }

        if ((int) ($data['version'] ?? 0) > CompetitionExportService::VERSION) {
            throw new \RuntimeException('備份檔版本比目前系統新，請先更新系統再匯入。');
        }

        if (empty($data['competition'])) {
            throw new \RuntimeException('備份檔缺少賽事資料。');
        }

        return $data;
    }

    private function restore(array $data, string $extractDir): Competition
    {
        $tables = $data['tables'] ?? [];

        // 1) 賽事本體
        $competitionId = $this->createCompetition($data['competition']);
        $competition = Competition::findOrFail($competitionId);

        // 2) 賽事類型
        $this->insertRows('competition_type', $tables['competition_type'] ?? [], function ($row) use ($competitionId) {
            $row['competition_id'] = $competitionId;

            return $row;
        });

        // 3) 組別 → 項目（programs 依賴 competition_categories.id）
        $categoryMap = $this->insertRows('competition_categories', $tables['competition_categories'] ?? [], function ($row) use ($competitionId) {
            $row['competition_id'] = $competitionId;

            return $row;
        });

        $programMap = $this->insertRows('programs', $tables['programs'] ?? [], function ($row) use ($categoryMap) {
            $row['competition_category_id'] = $this->requireMap(
                $categoryMap,
                $row['competition_category_id'] ?? null,
                'competition_categories'
            );

            return $row;
        });

        // 4) 隊伍 → 選手（athletes.team_id 沒有 FK，對應不到時保留原值）
        $teamMap = $this->insertRows('teams', $tables['teams'] ?? [], function ($row) use ($competitionId) {
            $row['competition_id'] = $competitionId;

            return $row;
        });

        $athleteMap = $this->insertRows('athletes', $tables['athletes'] ?? [], function ($row) use ($competitionId, $teamMap) {
            $row['competition_id'] = $competitionId;
            $row['team_id'] = $this->mapOptional($teamMap, $row['team_id'] ?? 0);

            return $row;
        });

        // 5) 報名（program_athlete）— bouts 會用它的 id
        $programAthleteMap = $this->insertRows('program_athlete', $tables['program_athlete'] ?? [], function ($row) use ($programMap, $athleteMap) {
            $row['program_id'] = $this->requireMap($programMap, $row['program_id'] ?? null, 'programs');
            $row['athlete_id'] = $this->requireMap($athleteMap, $row['athlete_id'] ?? null, 'athletes');

            return $row;
        });

        // 6) 場次（white/blue/winner = program_athlete.id；rise_to/rise_from = in_program_sequence 不變）
        $boutMap = $this->insertRows('bouts', $tables['bouts'] ?? [], function ($row) use ($programMap, $programAthleteMap) {
            $row['program_id'] = $this->requireMap($programMap, $row['program_id'] ?? null, 'programs');

            foreach (['white', 'blue', 'winner'] as $field) {
                if (array_key_exists($field, $row)) {
                    $row[$field] = $this->mapOptional($programAthleteMap, $row[$field]);
                }
            }

            return $row;
        });

        // 7) 成績
        $this->insertRows('bout_results', $tables['bout_results'] ?? [], function ($row) use ($boutMap) {
            $row['bout_id'] = $this->requireMap($boutMap, $row['bout_id'] ?? null, 'bouts');

            return $row;
        });

        // 8) 裁判（全域表：同 name + country 視為同一人，否則新建）
        $refereeMap = $this->resolveReferees($tables['referees'] ?? []);

        $competitionRefereeMap = $this->insertRows('competition_referee', $tables['competition_referee'] ?? [], function ($row) use ($competitionId, $refereeMap) {
            $row['competition_id'] = $competitionId;
            $row['referee_id'] = $this->mapOptional($refereeMap, $row['referee_id'] ?? 0);

            return $row;
        });

        // 9) 場次指派的裁判（JSON 陣列，存的是 competition_referee.id）
        $this->remapBoutReferees($tables['bouts'] ?? [], $boutMap, $competitionRefereeMap);

        // 10) 選手證列印設定（存在 configs 表，key 帶賽事 id）
        $idCardSettings = $data['configs']['id_card'] ?? null;

        if (is_array($idCardSettings) && $idCardSettings !== []) {
            $competition->updateIdCardSettings($idCardSettings);
        }

        // 11) 媒體檔（LOGO／抽籤背景／證明書／選手證背景…）
        $this->restoreMedia($competition, $data['media'] ?? [], $extractDir);

        return $competition;
    }

    private function createCompetition(array $row): int
    {
        $attributes = $this->prepareRow('competitions', $row);
        unset($attributes['id']);

        if (empty($attributes['name'])) {
            throw new \RuntimeException('備份檔缺少賽事名稱，無法匯入。');
        }

        // token 是公開查詢鍵，必須換新，避免和來源賽事（或既有賽事）撞號
        $attributes['token'] = Str::random(12);
        $attributes['created_at'] ??= now()->toDateTimeString();
        $attributes['updated_at'] ??= now()->toDateTimeString();

        return (int) DB::table('competitions')->insertGetId($attributes);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, int> 來源 id => 新 id
     */
    private function insertRows(string $table, array $rows, ?callable $transform = null): array
    {
        $map = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $oldId = (int) ($row['id'] ?? 0);

            if ($transform) {
                $row = $transform($row);
            }

            $map[$oldId] = $this->insert($table, $row);
        }

        return $map;
    }

    private function insert(string $table, array $row): int
    {
        $row = $this->prepareRow($table, $row);
        unset($row['id']);

        if ($row === []) {
            throw new \RuntimeException("匯入失敗：{$table} 的資料列是空的。");
        }

        return (int) DB::table($table)->insertGetId($row);
    }

    /**
     * 只留資料表真的有的欄位，並把（匯出時解碼過的）JSON 欄位編碼回字串。
     */
    private function prepareRow(string $table, array $row): array
    {
        return $this->encodeJsonColumns($table, $this->filterColumns($table, $row));
    }

    /**
     * 只保留資料表真的存在的欄位。
     */
    private function filterColumns(string $table, array $row): array
    {
        return array_intersect_key($row, array_flip(Schema::getColumnListing($table)));
    }

    /**
     * 把（匯出時解碼過的）JSON 欄位編碼回字串。
     */
    private function encodeJsonColumns(string $table, array $row): array
    {
        foreach (CompetitionExportService::JSON_COLUMNS[$table] ?? [] as $column) {
            if (! array_key_exists($column, $row)) {
                continue;
            }

            // null 保持 null（寫入 SQL NULL）；字串視為已經是 JSON 字串
            if ($row[$column] === null || is_string($row[$column])) {
                continue;
            }

            $row[$column] = json_encode($row[$column], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $row;
    }

    private function requireMap(array $map, $oldId, string $label): int
    {
        $oldId = (int) $oldId;

        if (! isset($map[$oldId])) {
            throw new \RuntimeException(
                "匯入失敗：找不到 {$label} 的對應資料（來源 id={$oldId}），備份檔可能不完整。"
            );
        }

        return $map[$oldId];
    }

    /**
     * 選填關聯：對應不到時保留原值（例如 bouts.white 的 0 / -1 這類哨兵值、
     * 或 athletes.team_id 這種沒有 FK 的欄位）。
     */
    private function mapOptional(array $map, $oldValue): int
    {
        $oldValue = (int) $oldValue;

        return $map[$oldValue] ?? $oldValue;
    }

    /**
     * referees 是全域表，先以 name + country 找既有資料，找不到才建立。
     *
     * @return array<int, int> 來源 referee id => 新 referee id
     */
    private function resolveReferees(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $oldId = (int) ($row['id'] ?? 0);
            $name = $row['name'] ?? null;
            $country = $row['country'] ?? null;

            $existing = $name
                ? DB::table('referees')->where('name', $name)->where('country', $country)->first()
                : null;

            $map[$oldId] = $existing
                ? (int) $existing->id
                : $this->insert('referees', $row);
        }

        return $map;
    }

    /**
     * bouts.competition_referee_ids 存的是一串 competition_referee.id，要換成新的 id。
     */
    private function remapBoutReferees(array $boutRows, array $boutMap, array $competitionRefereeMap): void
    {
        foreach ($boutRows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $oldBoutId = (int) ($row['id'] ?? 0);

            if (! isset($boutMap[$oldBoutId])) {
                continue;
            }

            $raw = $row['competition_referee_ids'] ?? null;

            if (! $raw) {
                continue;
            }

            $ids = is_array($raw) ? $raw : json_decode((string) $raw, true);

            if (! is_array($ids) || $ids === []) {
                continue;
            }

            $mapped = array_values(array_filter(array_map(
                fn ($id) => $competitionRefereeMap[(int) $id] ?? null,
                $ids
            )));

            DB::table('bouts')->where('id', $boutMap[$oldBoutId])->update([
                'competition_referee_ids' => $mapped === [] ? null : json_encode($mapped),
            ]);
        }
    }

    private function restoreMedia(Competition $competition, array $entries, string $extractDir): void
    {
        foreach ($entries as $entry) {
            if (! is_array($entry) || empty($entry['file'])) {
                continue;
            }

            $path = $extractDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $entry['file']);

            if (! is_file($path)) {
                continue;
            }

            $competition
                ->addMedia($path)
                ->preservingOriginal()
                ->usingFileName($entry['file_name'] ?? basename($path))
                ->usingName($entry['name'] ?? pathinfo($path, PATHINFO_FILENAME))
                ->toMediaCollection($entry['collection'] ?? 'default');
        }
    }
}
