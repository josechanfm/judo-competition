<?php

namespace App\Models;

use App\Services\BoutGenerationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;

class Competition extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasApiTokens;

    use HasFactory;
    public const STATUS_CREATED = 0;
    public const STATUS_ATHLETES_CONFIRMED = 1;
    public const STATUS_PROGRAMS_ARRANGED = 2;
    // 完成抽籤
    public const STATUS_SEAT_LOCKED = 3;
    // bout adjust
    public const STATUS_PROGRAM_STARTED = 4;
    public const STATUS_FINISH = 5;

    protected $fillable = ['competition_type_id', 'type_id', 'date_start', 'date_end', 'country', 'name', 'name_secondary', 'scale', 'days', 'remark', 'mat_number', 'section_number', 'language', 'is_language_secondary_enabled', 'system', 'small_system', 'type', 'gender', 'seeding', 'language_secondary', 'token', 'is_cancelled', 'status'];
    protected $casts = [
        'days' => 'json',
        'is_language_secondary_enabled' => 'boolean',
        'small_system' => 'json'
    ];

    public function programs()
    {
        return $this->hasManyThrough(Program::class, CompetitionCategory::class);
    }

    public function programsBouts()
    {
        return $this->hasManyThrough(Program::class, CompetitionCategory::class)->with('bouts');
    }
    public function bouts()
    {
        $programIds = $this->programs->pluck('id');
        return Bout::whereIn('program_id', $programIds);
        //return $this->hasManyThrough(Bout::class, Program::class);
    }

    public function filterAthletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function programAthletes()
    {
        // 先獲取所有相關的 programs
        $programIds = $this->programs->pluck('id');
        
        // 再獲取這些 programs 的 programAthletes
        return ProgramAthlete::whereIn('program_id', $programIds);
    }

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function referees()
    {
        return $this->hasMany(CompetitionReferee::class);
    }
    // public function programAthletes()
    // {
    //     return $this->hasMany(ProgramAthlete::class);
    // }
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
    public function categories()
    {
        return $this->hasMany(CompetitionCategory::class);
    }
    public function competition_type()
    {
        return $this->hasOne(CompetitionType::class);
    }
    public function generateBouts()
    {
        (new BoutGenerationService($this))->generate();
    }
    public function getDrawBackgroundUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('draw-background') == '' ? asset('assets/draw-background.jpg') : $this->getFirstMediaUrl('draw-background');
    }

    public function getDrawCoverUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('draw-cover') == '' ? asset('assets/draw-background.jpg') : $this->getFirstMediaUrl('draw-cover');
    }

    /**
     * 賽事 LOGO 的實際檔案路徑（給 mPDF / TCPDF 的 image() 用）。
     *
     * 注意：mPDF 不會把「相對專案根目錄」的路徑對應到 public/，
     * 例如 'images/jua_logo.png' 會直接噴 Could not find image file，
     * 所以這裡一律回傳絕對路徑。
     *
     * @param  string|null  $fallback  沒有上傳 LOGO 時要用的圖（相對 public 的路徑），null = 不用預設圖
     */
    public function logoPath(?string $fallback = 'images/jua_logo.png'): ?string
    {
        $media = $this->getFirstMedia('logo');

        if ($media && is_file($media->getPath())) {
            return str_replace('\\', '/', $media->getPath());
        }

        if (!$fallback || $this->isAbsolutePath($fallback)) {
            return $fallback;
        }

        return str_replace('\\', '/', public_path($fallback));
    }

    /**
     * 運動員證 (ID card) 的背景圖：只用「賽事上傳的自訂背景」，
     * 沒有上傳時退回預設檔，避免卡片全白。
     */
    public const ID_CARD_DEFAULT_BACKGROUND = 'id-card-bg3.jpg';

    /**
     * 運動員證的版面尺寸（單位 mm，與 AthletePdfService 一致）
     */
    public const ID_CARD_PAGE_WIDTH = 210;      // A4 寬
    public const ID_CARD_CARD_HEIGHT = 148.5;   // 一頁兩張，每張高度
    public const ID_CARD_LEFT_MARGIN = 28;      // 卡片左邊界
    public const ID_CARD_FIELD_WIDTH = 50;      // 欄位文字框寬度（文字置中）

    /**
     * TCPDF 的 K_CELL_HEIGHT_RATIO（tcpdf_config.php）。
     * Cell() 的最小高度是「字級 × 這個比例」，設定頁的預覽要用同一個比例才會對齊。
     */
    public const ID_CARD_CELL_HEIGHT_RATIO = 1.25;

    /**
     * 可以調整的欄位：位置（相對卡片左上角 mm）、字級（pt）、是否顯示、文字顏色
     */
    public const ID_CARD_FIELDS = ['name', 'name_secondary', 'category', 'team'];

    /** 顏色＝auto 的意思：組別＝男藍女紅，其他欄位＝黑色 */
    public const ID_CARD_COLOR_AUTO = 'auto';

    public const ID_CARD_DEFAULT_FIELD_SETTINGS = [
        'name' => ['x' => 10.0, 'y' => 70.0, 'size' => 18, 'visible' => true, 'color' => '#000000'],
        'name_secondary' => ['x' => 10.0, 'y' => 80.0, 'size' => 16, 'visible' => true, 'color' => '#000000'],
        'category' => ['x' => 7.0, 'y' => 94.0, 'size' => 22, 'visible' => true, 'color' => self::ID_CARD_COLOR_AUTO],
        'team' => ['x' => 6.0, 'y' => 115.0, 'size' => 18, 'visible' => true, 'color' => '#000000'],
    ];

    /**
     * 顏色的合法值只有 "auto" 或 #rrggbb，其他一律退回預設值。
     */
    public static function normalizeIdCardColor($color, string $fallback = self::ID_CARD_COLOR_AUTO): string
    {
        $color = strtolower(trim((string) $color));

        if ($color === self::ID_CARD_COLOR_AUTO) {
            return $color;
        }

        // #abc → #aabbcc
        if (preg_match('/^#?([0-9a-f]{3})$/', $color, $matches)) {
            return sprintf(
                '#%s%s%s%s%s%s',
                $matches[1][0],
                $matches[1][0],
                $matches[1][1],
                $matches[1][1],
                $matches[1][2],
                $matches[1][2]
            );
        }

        return preg_match('/^#[0-9a-f]{6}$/', $color) ? $color : $fallback;
    }

    /**
     * #rrggbb → [r, g, b]。auto 由呼叫端自行處理（這裡當黑色）。
     */
    public static function idCardColorRgb($color): array
    {
        $color = self::normalizeIdCardColor($color, '#000000');

        if ($color === self::ID_CARD_COLOR_AUTO) {
            return [0, 0, 0];
        }

        return [
            hexdec(substr($color, 1, 2)),
            hexdec(substr($color, 3, 2)),
            hexdec(substr($color, 5, 2)),
        ];
    }

    /**
     * 運動員證的列印設定（存在 configs 表，不額外開欄位）。
     */
    public function idCardSettings(): array
    {
        // 注意：Config::item() 是 json_decode() 不加 true，回傳的是 object（巢狀也會是 object）
        $settings = (array) (Config::item($this->idCardSettingsKey()) ?: []);
        $storedFields = (array) ($settings['fields'] ?? []);

        $fields = [];

        foreach (self::ID_CARD_FIELDS as $field) {
            $stored = (array) ($storedFields[$field] ?? []);
            $default = self::ID_CARD_DEFAULT_FIELD_SETTINGS[$field];

            $fields[$field] = [
                'x' => round((float) ($stored['x'] ?? $default['x']), 1),
                'y' => round((float) ($stored['y'] ?? $default['y']), 1),
                'size' => round((float) ($stored['size'] ?? $default['size']), 1),
                'visible' => (bool) ($stored['visible'] ?? $default['visible']),
                'color' => self::normalizeIdCardColor($stored['color'] ?? null, $default['color']),
            ];
        }

        return ['fields' => $fields];
    }

    public function idCardSettingsKey(): string
    {
        return "competition.{$this->id}.id_card";
    }

    public function updateIdCardSettings(array $settings): array
    {
        Config::updateOrCreate(
            ['key' => $this->idCardSettingsKey()],
            ['value' => json_encode(array_merge($this->idCardSettings(), $settings))]
        );

        return $this->idCardSettings();
    }

    /**
     * 運動員證背景圖的檔案路徑（TCPDF 需要絕對路徑）。
     *
     * 有上傳自訂背景就用它，否則用預設背景檔。
     */
    public function idCardBackgroundPath(): string
    {
        $media = $this->getFirstMedia('id-card-background');

        if ($media && is_file($media->getPath())) {
            return str_replace('\\', '/', $media->getPath());
        }

        return str_replace('\\', '/', public_path('images/' . self::ID_CARD_DEFAULT_BACKGROUND));
    }

    /**
     * 運動員證背景圖的網址（給設定頁預覽用）
     */
    public function idCardBackgroundUrl(): string
    {
        $media = $this->getFirstMedia('id-card-background');

        if ($media) {
            return $media->getUrl();
        }

        return asset('images/' . self::ID_CARD_DEFAULT_BACKGROUND);
    }

    /**
     * 判斷是不是絕對路徑（含 Windows 磁碟機路徑與 URL）
     */
    public static function isAbsolutePath(?string $path): bool
    {
        return (bool) $path && (
            str_starts_with($path, '/')
            || preg_match('#^[a-zA-Z]:[\\\\/]#', $path)
            || str_contains($path, '://')
        );
    }
}
