<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

/**
 * 把一整場賽事（含組別／項目／隊伍／選手／報名／抽籤／場次／成績／裁判／媒體）
 * 打包成單一 ZIP（內含 data.json + media/）以便備份或轉移到另一個資料庫。
 *
 * 匯入端請搭配 {@see CompetitionImportService}，兩邊的 key 必須一致。
 */
class CompetitionExportService
{
    /** 檔案格式識別碼（匯入時會檢查） */
    public const FORMAT = 'judo-competition-backup';

    /** 格式版本；之後若結構有破壞性變更請 +1 */
    public const VERSION = 1;

    /** ZIP 內的資料檔名 */
    public const DATA_FILE = 'data.json';

    /** ZIP 內的媒體目錄 */
    public const MEDIA_DIR = 'media';

    /**
     * 賽事相關的資料表（不含 competitions 本身）。
     * 順序 = 匯入時的建立順序（被參考的先建立）。
     */
    public const RELATED_TABLES = [
        'competition_type',
        'competition_categories',
        'programs',
        'teams',
        'athletes',
        'program_athlete',
        'bouts',
        'bout_results',
        'referees',
        'competition_referee',
    ];

    /**
     * 這些欄位在 DB 是 JSON 型別，匯出時解碼成陣列（匯入時會再編碼回去），
     * 讓 data.json 保持人類可讀。
     *
     * 注意：新增 JSON 欄位時要一起加在這裡，否則會以原始字串（跳脫過的 JSON）匯出。
     */
    public const JSON_COLUMNS = [
        'competitions' => ['days', 'small_system'],
        'competition_categories' => ['weights'],
        'bout_results' => ['actions'],
    ];

    /**
     * 匯出並產生暫存 ZIP。
     *
     * @return array{path: string, filename: string}
     */
    public function export(Competition $competition): array
    {
        $data = $this->buildData($competition);

        $dir = $this->tempDir();

        if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir)) {
            throw new \RuntimeException("無法建立暫存目錄：{$dir}");
        }

        $zipPath = $dir . DIRECTORY_SEPARATOR . uniqid('competition-export-', true) . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('無法建立暫存壓縮檔。');
        }

        $data['media'] = $this->addMediaFiles($zip, $competition);

        $zip->addFromString(
            self::DATA_FILE,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
        $zip->close();

        return [
            'path' => $zipPath,
            'filename' => $this->downloadName($competition),
        ];
    }

    /**
     * 組出要寫進 data.json 的結構（不含 media，media 由 export() 補上）。
     */
    public function buildData(Competition $competition): array
    {
        $categoryIds = DB::table('competition_categories')
            ->where('competition_id', $competition->id)
            ->pluck('id')
            ->all();

        $programIds = $this->pluckIn('programs', 'id', 'competition_category_id', $categoryIds);
        $boutIds = $this->pluckIn('bouts', 'id', 'program_id', $programIds);

        $refereeIds = DB::table('competition_referee')
            ->where('competition_id', $competition->id)
            ->pluck('referee_id')
            ->all();

        return [
            'format' => self::FORMAT,
            'version' => self::VERSION,
            'exported_at' => now()->toIso8601String(),
            'source_competition_id' => $competition->id,
            'competition' => $this->fetchRow('competitions', $competition->id),
            'configs' => $this->buildConfigs($competition),
            'media' => [],
            'tables' => [
                'competition_type' => $this->fetch('competition_type', fn ($q) => $q->where('competition_id', $competition->id)),
                'competition_categories' => $this->fetch('competition_categories', fn ($q) => $q->where('competition_id', $competition->id)),
                'programs' => $this->fetch('programs', fn ($q) => $q->whereIn('competition_category_id', $this->orDummy($categoryIds))),
                'teams' => $this->fetch('teams', fn ($q) => $q->where('competition_id', $competition->id)),
                'athletes' => $this->fetch('athletes', fn ($q) => $q->where('competition_id', $competition->id)),
                'program_athlete' => $this->fetch('program_athlete', fn ($q) => $q->whereIn('program_id', $this->orDummy($programIds))),
                'bouts' => $this->fetch('bouts', fn ($q) => $q->whereIn('program_id', $this->orDummy($programIds))),
                'bout_results' => $this->fetch('bout_results', fn ($q) => $q->whereIn('bout_id', $this->orDummy($boutIds))),
                'referees' => $this->fetch('referees', fn ($q) => $q->whereIn('id', $this->orDummy($refereeIds))),
                'competition_referee' => $this->fetch('competition_referee', fn ($q) => $q->where('competition_id', $competition->id)),
            ],
        ];
    }

    /**
     * 賽事的媒體檔（LOGO／抽籤背景／證明書／選手證背景…）全部複製進 ZIP。
     *
     * 注意：一定要用 `$competition->media`（morphMany 關聯）。
     * spatie v10 的 `getMedia()` 預設參數是 `'default'` 而不是 `'*'`，
     * 賽事又沒有註冊任何 media collection，所以 `getMedia()` 會回傳空集合。
     *
     * @return array<int, array{collection: string, file: string, name: string, file_name: string}>
     */
    private function addMediaFiles(ZipArchive $zip, Competition $competition): array
    {
        $entries = [];
        $index = 0;

        foreach ($competition->media as $media) {
            try {
                $path = $media->getPath();
            } catch (\Throwable $e) {
                // 檔案不在磁碟上（例如已被手動刪除）→ 略過
                continue;
            }

            if (! is_file($path)) {
                continue;
            }

            $localName = sprintf(
                '%s/%s/%d-%s',
                self::MEDIA_DIR,
                $media->collection_name,
                $index++,
                $media->file_name
            );

            $zip->addFile($path, $localName);

            $entries[] = [
                'collection' => $media->collection_name,
                'file' => $localName,
                'name' => $media->name,
                'file_name' => $media->file_name,
            ];
        }

        return $entries;
    }

    /**
     * 賽事層級的設定（存在 configs 表，key 帶賽事 id）。
     * 目前只有選手證列印設定，key = competition.{id}.id_card。
     */
    private function buildConfigs(Competition $competition): array
    {
        $configs = [];

        $idCard = Config::item($competition->idCardSettingsKey());

        if ($idCard) {
            $configs['id_card'] = $idCard;
        }

        return $configs;
    }

    private function tempDir(): string
    {
        return storage_path('app/competition-transfer');
    }

    private function downloadName(Competition $competition): string
    {
        return sprintf('competition-%d-%s.zip', $competition->id, now()->format('Ymd-His'));
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array<int, int>
     */
    private function pluckIn(string $table, string $column, string $foreignKey, array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return DB::table($table)->whereIn($foreignKey, $ids)->pluck($column)->all();
    }

    /**
     * whereIn() 空陣列會變成「永遠不成立」，但查詢仍會執行；
     * 傳 [] 會讓 SQL 變成 whereIn ('x', [])，MySQL 支援，這裡保留可讀性。
     *
     * @param  array<int, mixed>  $ids
     * @return array<int, mixed>
     */
    private function orDummy(array $ids): array
    {
        return $ids ?: [0];
    }

    private function fetchRow(string $table, int $id): array
    {
        $row = DB::table($table)->find($id);

        return $row ? $this->hydrate($table, (array) $row) : [];
    }

    /**
     * @param  callable(\Illuminate\Database\Query\Builder): \Illuminate\Database\Query\Builder  $constraint
     */
    private function fetch(string $table, callable $constraint): array
    {
        return $constraint(DB::table($table))
            ->get()
            ->map(fn ($row) => $this->hydrate($table, (array) $row))
            ->all();
    }

    /**
     * 只保留資料表真的存在的欄位（避免不同版本 schema 差異），
     * 並把 JSON 欄位解碼成陣列。
     */
    private function hydrate(string $table, array $row): array
    {
        $row = array_intersect_key($row, array_flip(Schema::getColumnListing($table)));

        foreach (self::JSON_COLUMNS[$table] ?? [] as $column) {
            if (array_key_exists($column, $row) && is_string($row[$column])) {
                $row[$column] = json_decode($row[$column], true);
            }
        }

        return $row;
    }
}
