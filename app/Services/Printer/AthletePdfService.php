<?php

namespace App\Services\Printer;

use TCPDF;
use App\Helpers\PdfHelper;
use App\Models\Competition;
use Illuminate\Support\Facades\Storage;

class AthletePdfService
{
    private $pdf;
    private $bgImagePath;
    private $cardWidth = 210; // 完整A4寬度
    private $cardHeight = 297; // 完整A4高度

    /** 卡片上各欄位的設定（位置 mm、字級 pt、是否顯示），可由設定頁調整 */
    private $fieldSettings = Competition::ID_CARD_DEFAULT_FIELD_SETTINGS;

    public function __construct()
    {
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetCreator('Sports Club');
        $this->pdf->SetAuthor('Sports Club');
        $this->pdf->SetTitle('Athlete ID Cards');
        $this->pdf->SetMargins(0, 0, 0);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        $this->bgImagePath = public_path('images/' . Competition::ID_CARD_DEFAULT_BACKGROUND);
    }

    /**
     * 套用賽事在設定頁調好的運動員證設定（背景圖與各欄位位置）。
     */
    public function useCompetitionIdCardSettings(?Competition $competition): static
    {
        if (!$competition) {
            return $this;
        }

        $this->bgImagePath = $competition->idCardBackgroundPath();
        $this->fieldSettings = $competition->idCardSettings()['fields'];

        return $this;
    }

    public function generateIdCard($athletes)
    {
        // 每頁只顯示兩個運動員（左側兩個位置）
        $positions = [
            ['x' => Competition::ID_CARD_LEFT_MARGIN, 'y' => 0],                                        // 左上
            ['x' => Competition::ID_CARD_LEFT_MARGIN, 'y' => Competition::ID_CARD_CARD_HEIGHT],         // 左下
        ];

        $currentPosition = 0;
        $pageAdded = false;

        foreach ($athletes as $index => $athlete) {
            // 每2個運動員或新頁面開始時添加新頁面
            if ($currentPosition === 0 && !$pageAdded) {
                $this->pdf->AddPage();
                $pageAdded = true;
                
                // 添加整頁背景圖片
                $this->addFullPageBackground();
            }

            // 獲取當前位置
            if (isset($positions[$currentPosition])) {
                $pos = $positions[$currentPosition];
                $this->generateOtherSingleCard($athlete, $pos['x'], $pos['y']);
            }

            // 更新位置計數器
            $currentPosition++;

            // 如果當前位置達到2，重置計數器並標記需要新頁面
            if ($currentPosition >= 2) {
                $currentPosition = 0;
                $pageAdded = false;
            }
        }

        return $this->pdf;
    }

    public function generateOneIdCard($athlete)
    {
        // 每頁只顯示兩個運動員（左側兩個位置）
        $positions = [
            ['x' => Competition::ID_CARD_LEFT_MARGIN, 'y' => 0],                                // 左上
            ['x' => Competition::ID_CARD_LEFT_MARGIN, 'y' => Competition::ID_CARD_CARD_HEIGHT], // 左下
        ];

        $currentPosition = 0;
        $pageAdded = false;

        if ($currentPosition === 0 && !$pageAdded) {
            $this->pdf->AddPage('L', [Competition::ID_CARD_PAGE_WIDTH, Competition::ID_CARD_CARD_HEIGHT]);
            $pageAdded = true;

            // 添加整頁背景圖片
            $this->addA5Background();
        }

        // 獲取當前位置
        if (isset($positions[$currentPosition])) {
            $pos = $positions[$currentPosition];
            $this->generateOtherSingleCard($athlete, $pos['x'], $pos['y']);
        }

        // 更新位置計數器
        $currentPosition++;

        // 如果當前位置達到2，重置計數器並標記需要新頁面
        if ($currentPosition >= 2) {
            $currentPosition = 0;
            $pageAdded = false;
        }

        return $this->pdf->Output('filename.pdf', 'S');
    }

    private function addFullPageBackground()
    {
        // 一頁兩張：上半 0mm、下半 148.5mm。高度必須用 148.5（不能用 148），
        // 否則下半張的背景會比文字高 0.5mm，看起來就會「少少偏移」。
        $height = Competition::ID_CARD_CARD_HEIGHT;

        foreach ([0, $height] as $y) {
            $this->drawCardBackground(0, $y);
        }
    }

    /**
     * 預覽用的單張背景（頁面本身就是 210 x 148.5 的橫向頁）
     */
    private function addA5Background()
    {
        $this->drawCardBackground(0, 0);
    }

    /**
     * 畫一張卡片大小的背景圖。
     * 圖片類型刻意留空，讓 TCPDF 自己判斷（上傳 PNG 時就不會壞掉）。
     */
    private function drawCardBackground(float $x, float $y): void
    {
        if (!file_exists($this->bgImagePath)) {
            return;
        }

        $this->pdf->Image(
            $this->bgImagePath,
            $x,
            $y,
            Competition::ID_CARD_PAGE_WIDTH,
            Competition::ID_CARD_CARD_HEIGHT,
            '', // 由 TCPDF 判斷圖片格式（jpg / png 都可以）
            '',
            '',
            false,
            300,
            '',
            false,
            false,
            0,
            false,
            false,
            false
        );
    }

    /**
     * 注意：這個方法目前沒有被任何地方呼叫（卡片繪製是用 generateOtherSingleCard）。
     */
    private function generateSingleCard($athlete, $startX, $startY)
    {
        // 設置卡片區域
        $this->pdf->setPageMark();

        // 注意：這裡不再添加單獨的背景圖片，因為已經有整頁背景

        // 檢查是否有 name_secondary
        $hasSecondaryName = !empty($athlete->name_secondary);

        // 定義字段位置和寬度 - 調整x坐標以適應左側位置
        $fields = [
            'name' => [
                'x' => $startX, 
                'y' => $startY + 77,
                'width' => 50
            ],
            'name_secondary' => [
                'x' => $startX, 
                'y' => $startY + 88,
                'width' => 50
            ],
            'programCategoryWeight' => [
                'x' => $startX, 
                'y' => $startY + 100,
                'width' => 50
            ],
            'team' => [
                'x' => $startX, 
                'y' => $startY + 55,
                'width' => 50
            ],
            'abbreviation' => [
                'x' => $startX,
                'y' => $startY + 66,
                'width' => 50
            ]
        ];

                // 設置字體
        $this->pdf->SetFont('notoserifcjkhk', 'BU', 18);
        // 添加運動員數據
        $this->addField($fields['name'], $athlete->name);
        
        // 只有在有 name_secondary 時才顯示
        if ($hasSecondaryName) {
            if(mb_strlen($athlete->name_secondary) > 40){
                $this->pdf->SetFont('notoserifcjkhk', 'U', 7);
            }else if(mb_strlen($athlete->name_secondary) > 35){
                $this->pdf->SetFont('notoserifcjkhk', 'U', 8);
            }else if(mb_strlen($athlete->name_secondary) > 30){
                $this->pdf->SetFont('notoserifcjkhk', 'U', 9);
            }else if(mb_strlen($athlete->name_secondary) > 25){
                $this->pdf->SetFont('notoserifcjkhk', 'U', 10);
            }else if(mb_strlen($athlete->name_secondary) > 20){
                $this->pdf->SetFont('notoserifcjkhk', 'U', 13);
            }else {
                $this->pdf->SetFont('notoserifcjkhk', 'U', 16);
            }
            $this->addField($fields['name_secondary'], $athlete->name_secondary);
        }
        if ($athlete->gender == 'M') {
            $this->pdf->SetTextColor(0, 0, 255);
        } else {
            $this->pdf->SetTextColor(255, 0, 0);
        }
        $this->pdf->SetFont('notoserifcjkhk', 'B', 24);
        $this->addField($fields['programCategoryWeight'], ($athlete->programCategoryWeight ?? ''));
        $this->pdf->setTextColor(0,0,0);
        $this->pdf->SetFont('notoserifcjkhk', 'B', 18);
        $this->addField($fields['team'], ($athlete->team->name ?? ''));
        $this->addField($fields['abbreviation'], ($athlete->team->abbreviation ?? ''));
        if (!empty($athlete->photo) && is_string($athlete->photo)) {
            $this->addPhoto($athlete->photo, $startX + 65, $startY + 25, 30, 40);
        }

        if (!empty($athlete->id_number)) {
            $this->addQrCode($athlete->id_number, $startX + 70, $startY + 110, 25);
        }
    }

    private function generateOtherSingleCard($athlete, $startX, $startY)
    {
        // 設置卡片區域
        $this->pdf->setPageMark();

        // 注意：這裡不再添加單獨的背景圖片，因為已經有整頁背景

        // 欄位位置與字級由設定頁決定，這裡換算成頁面座標
        $position = function (string $field) use ($startX, $startY): array {
            $settings = $this->fieldSettings[$field] ?? Competition::ID_CARD_DEFAULT_FIELD_SETTINGS[$field];

            return [
                'x' => $startX + (float) $settings['x'],
                'y' => $startY + (float) $settings['y'],
                'width' => Competition::ID_CARD_FIELD_WIDTH,
            ];
        };

        $isVisible = fn (string $field): bool => (bool) ($this->fieldSettings[$field]['visible']
            ?? Competition::ID_CARD_DEFAULT_FIELD_SETTINGS[$field]['visible']);

        $fontSize = fn (string $field): float => (float) ($this->fieldSettings[$field]['size']
            ?? Competition::ID_CARD_DEFAULT_FIELD_SETTINGS[$field]['size']);

        // 文字顏色：設定頁可指定 #rrggbb，或 auto（組別＝男藍女紅，其他欄位＝黑）
        $color = fn (string $field): array => $this->fieldTextColor($field, $athlete);

        // 第一語言姓名
        if ($isVisible('name')) {
            $this->pdf->SetTextColor(...$color('name'));
            $this->pdf->SetFont('notoserifcjkhk', 'BU', $fontSize('name'));
            $this->addField($position('name'), $athlete->name);
        }

        // 第二語言姓名（字太長會自動縮小，但不會超過設定字級）
        if ($isVisible('name_secondary') && !empty($athlete->name_secondary)) {
            $this->pdf->SetTextColor(...$color('name_secondary'));
            $this->pdf->SetFont(
                'notoserifcjkhk',
                'U',
                min($fontSize('name_secondary'), $this->secondaryNameFontSize($athlete->name_secondary))
            );
            $this->addField($position('name_secondary'), $athlete->name_secondary);
        }

        // 組別（顏色沒設定時，依性別上色：男子藍、女子紅）
        if ($isVisible('category')) {
            $this->pdf->SetTextColor(...$color('category'));
            $this->pdf->SetFont('notoserifcjkhk', 'B', $fontSize('category'));
            $this->addField($position('category'), ($athlete->programCategoryWeight ?? ''));
        }

        // 學校 / 隊伍名
        if ($isVisible('team')) {
            $this->pdf->SetTextColor(...$color('team'));
            $this->pdf->SetFont('notoserifcjkhk', 'B', $fontSize('team'));
            $this->addField($position('team'), ($athlete->team->name ?? ''));
        }

        // 後面的卡片不要被前一張的顏色影響
        $this->pdf->SetTextColor(0, 0, 0);
    }

    /**
     * 欄位文字顏色（RGB）。設定值為 auto 時：組別依性別（男藍女紅），其他欄位黑色。
     */
    private function fieldTextColor(string $field, $athlete): array
    {
        $setting = $this->fieldSettings[$field]['color']
            ?? Competition::ID_CARD_DEFAULT_FIELD_SETTINGS[$field]['color'];

        if ($setting !== Competition::ID_CARD_COLOR_AUTO) {
            return Competition::idCardColorRgb($setting);
        }

        if ($field === 'category') {
            return $athlete->gender == 'M' ? [0, 0, 255] : [255, 0, 0];
        }

        return [0, 0, 0];
    }

    /**
     * 第二語言姓名太長時要用的字級上限（原本的行為）
     */
    private function secondaryNameFontSize(string $name): float
    {
        return match (true) {
            mb_strlen($name) > 40 => 7,
            mb_strlen($name) > 35 => 8,
            mb_strlen($name) > 30 => 9,
            mb_strlen($name) > 25 => 10,
            mb_strlen($name) > 20 => 13,
            default => 16,
        };
    }

    private function addField($position, $text)
    {
        if (is_array($position) && isset($position['x']) && isset($position['y'])) {
            $this->pdf->SetXY($position['x'], $position['y']);

            $this->pdf->Cell($position['width'], 0, $text, 0, 1, 'C');
        }
    }

    private function addPhoto($photoPath, $x, $y, $width, $height)
    {
        $fullPath = public_path($photoPath);
        if (file_exists($fullPath) && is_file($fullPath)) {
            try {
                $this->pdf->Image(
                    $fullPath,
                    $x,
                    $y,
                    $width,
                    $height,
                    '',
                    '',
                    '',
                    false,
                    300,
                    '',
                    false,
                    false,
                    0,
                    false,
                    false,
                    false
                );
            } catch (\Exception $e) {
                \Log::error('PDF圖片加載失敗: ' . $e->getMessage());
            }
        }
    }

    private function addQrCode($data, $x, $y, $size)
    {
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        );
        
        try {
            $this->pdf->write2DBarcode($data, 'QRCODE,L', $x, $y, $size, $size, $style, 'N');
        } catch (\Exception $e) {
            \Log::error('PDF二維碼生成失敗: ' . $e->getMessage());
        }
    }

    private function truncateText($text, $maxLength)
    {
        if (mb_strlen($text) > $maxLength) {
            return mb_substr($text, 0, $maxLength) . '...';
        }
        return $text;
    }

    private function smartTruncate($name, $maxLength = 14)
    {
        if (mb_strlen($name) <= $maxLength) {
            return $name;
        }
        
        // 葡文名字通常格式：名 姓
        $parts = explode(' ', $name);
        
        if (count($parts) >= 2) {
            // 先嘗試：前面的部分都只保留首字母，最後一個部分保持完整
            $shortName = '';
            for ($i = 0; $i < count($parts) - 1; $i++) {
                if (!empty($parts[$i])) {
                    $shortName .= mb_substr($parts[$i], 0, 1) . '.';
                }
            }
            
            // 加上完整的姓氏
            $shortName .= ' ' . end($parts);
            
            if (mb_strlen($shortName) <= $maxLength) {
                return $shortName;
            }
            
            // 如果還是太長，使用最簡格式：第一個名字的首字母 + 完整姓氏
            $firstName = mb_substr($parts[0], 0, 1) . '.';
            $lastName = end($parts);
            $simplestName = $firstName . ' ' . $lastName;
            
            if (mb_strlen($simplestName) <= $maxLength) {
                return $simplestName;
            }
        }
        
        // 如果還是太長，直接截斷
        return mb_substr($name, 0, $maxLength - 3) . '...';
    }
}