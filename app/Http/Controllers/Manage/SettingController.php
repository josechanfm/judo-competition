<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\Config;
use App\Models\Team;
use App\Services\Printer\AthletePdfService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class SettingController extends Controller
{
    //

    public function index(Competition $competition)
    {
        $competition->makeVisible('token');
        $competition->load([
            'competition_type',
            'categories',
            // 只取出畫面需要的欄位，不要把 access token 的雜湊值送到前端
            'tokens' => fn ($query) => $query->select(
                'id',
                'tokenable_type',
                'tokenable_id',
                'name',
                'last_used_at'
            ),
        ]);

        return Inertia::render('Manage/Settings/Index', [
            'competition' => fn () => $competition,

            'languages' => fn () => Config::item('languages') ?: [],

            'draw' => fn () => [
                'background' => $competition->getDrawBackgroundUrlAttribute(),
                'cover' => $competition->getDrawCoverUrlAttribute(),
            ],

            'logoUrl' => fn () => $competition->getFirstMediaUrl('logo')
                ?: asset('assets/icon.png'),

            'certificateUrl' => fn () => $competition->getFirstMediaUrl('certificate')
                ?: asset('assets/certificate.png'),

            'idCard' => fn () => [
                'settings' => $competition->idCardSettings(),
                'backgroundUrl' => $competition->idCardBackgroundUrl(),
                'customUrl' => $competition->getFirstMediaUrl('id-card-background') ?: null,
                // 給前端換算拖曳座標（1 單位 = 1mm）
                'geometry' => [
                    'pageWidth' => Competition::ID_CARD_PAGE_WIDTH,
                    'cardHeight' => Competition::ID_CARD_CARD_HEIGHT,
                    'leftMargin' => Competition::ID_CARD_LEFT_MARGIN,
                    'fieldWidth' => Competition::ID_CARD_FIELD_WIDTH,
                    // 預覽與 PDF 的垂直對齊用同一個列高比例
                    'cellHeightRatio' => Competition::ID_CARD_CELL_HEIGHT_RATIO,
                ],
                'fields' => Competition::ID_CARD_FIELDS,
            ],
        ]);
    }

    /**
     * 更新運動員證的欄位設定（位置、字級、是否顯示、文字顏色）。
     */
    public function updateIdCardSettings(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'fields' => ['required', 'array'],
            // 位置與字級只擋極端值，允許使用者自由輸入
            'fields.*.x' => ['required', 'numeric', 'min:0', 'max:300'],
            'fields.*.y' => ['required', 'numeric', 'min:0', 'max:300'],
            'fields.*.size' => ['required', 'numeric', 'min:1', 'max:200'],
            'fields.*.visible' => ['required', 'boolean'],
            // 允許舊版前端沒帶顏色，這時會用該欄位的預設色
            'fields.*.color' => ['nullable', 'string', 'max:9'],
        ]);

        // 只接受已知欄位，顏色統一成 auto 或 #rrggbb
        $fields = array_intersect_key($validated['fields'], array_flip(Competition::ID_CARD_FIELDS));

        foreach ($fields as $field => $setting) {
            $fields[$field]['color'] = Competition::normalizeIdCardColor(
                $setting['color'] ?? null,
                Competition::ID_CARD_DEFAULT_FIELD_SETTINGS[$field]['color']
            );
        }

        $competition->updateIdCardSettings(['fields' => $fields]);

        return redirect()->back();
    }

    /**
     * 產生一張範例運動員證，讓設定頁可以預覽目前的位置設定。
     */
    public function previewIdCard(Competition $competition)
    {
        $service = new AthletePdfService();
        $service->useCompetitionIdCardSettings($competition);

        $pdf = $service->generateOneIdCard($this->sampleIdCardAthlete($competition));

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="id-card-preview.pdf"',
        ]);
    }

    /**
     * 預覽用的範例運動員：優先用賽事裡真實的選手，沒有就造一筆假的。
     */
    private function sampleIdCardAthlete(Competition $competition): Athlete
    {
        $programAthlete = $competition->programAthletes()
            ->with(['athlete.team', 'program.competitionCategory'])
            ->first();

        if ($programAthlete?->athlete && $programAthlete->program) {
            $athlete = clone $programAthlete->athlete;
            $program = $programAthlete->program;

            $athlete->programCategoryWeight = $program->convertGender()
                . $program->competitionCategory->name . $program->convertWeight();

            return $athlete;
        }

        $athlete = new Athlete([
            'name' => '陳大文',
            'name_secondary' => 'CHAN TAI MAN',
            'gender' => 'M',
        ]);

        $athlete->programCategoryWeight = '男子A組-60kg';
        $athlete->setRelation('team', new Team(['name' => '範例學校']));

        return $athlete;
    }

    /**
     * 上傳自訂的運動員證背景圖。
     */
    public function updateIdCardBackground(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $this->replaceMedia($competition, $request, 'id-card-background', 'file', true);
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param Competition $competition
     * @return RedirectResponse
     */
    public function updateLogo(Request $request, Competition $competition)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png'],
        ]);

        try {
            $this->replaceMedia($competition, $request, 'logo', 'logo');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }

    /**
     * 以新上傳的檔案取代原有媒體。
     *
     * spatie 的 getFirstMediaUrl() 只會回傳集合裡的「第一筆」，
     * 若不清掉舊的就直接新增，重複上傳後畫面會一直顯示最舊的那張圖。
     */
    private function replaceMedia(
        Competition $competition,
        Request $request,
        string $collection,
        string $inputName = 'file',
        bool $keepOriginalExtension = false
    ): void {
        $competition->clearMediaCollection($collection);

        $extension = $keepOriginalExtension
            ? ($request->file($inputName)->getClientOriginalExtension() ?: 'png')
            : 'png';

        $competition->addMedia($request->file($inputName))
            ->usingFileName(uniqid($collection . '-') . '.' . $extension)
            ->toMediaCollection($collection);
    }

    public function updateDrawBackground(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $this->replaceMedia($competition, $request, 'draw-background');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }

    public function updateCertificate(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $this->replaceMedia($competition, $request, 'certificate');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }
    }

    public function updateDrawCover(Request $request, Competition $competition)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            $this->replaceMedia($competition, $request, 'draw-cover');
        } catch (FileDoesNotExist $e) {
            return redirect()->back()->with('message', 'File does not exist.');
        } catch (FileIsTooBig $e) {
            return redirect()->back()->with('message', 'File is too big.');
        }

        return redirect()->back();
    }

    /**
     * 更新賽事語言設定。
     * 語言欄位實際存在 competition_type 資料表，不是在 competitions 上。
     */
    public function updateLanguage(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'language' => ['required'],
            'is_language_secondary_enabled' => ['required', 'boolean'],
            'language_secondary' => ['nullable'],
        ]);

        $competition->competition_type()->update([
            'language' => $validated['language'],
            'is_language_secondary_enabled' => $validated['is_language_secondary_enabled'],
            // 關閉第二語言時一併清掉，避免留下舊值
            'language_secondary' => $validated['is_language_secondary_enabled']
                ? ($validated['language_secondary'] ?? null)
                : null,
        ]);

        return redirect()->back();
    }

    public function removeDevice(Request $request, Competition $competition, string $uuid)
    {
        $competition->tokens()->where('name', $uuid)->delete();

        return redirect()->back();
    }
}
