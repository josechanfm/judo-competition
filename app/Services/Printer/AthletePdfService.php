<?php

namespace App\Services\Printer;

use TCPDF;
use App\Helpers\PdfHelper;
use Illuminate\Support\Facades\Storage;

class AthletePdfService
{
    private $pdf;
    private $bgImagePath;
    private $cardWidth = 210; // 完整A4寬度
    private $cardHeight = 297; // 完整A4高度

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

        $this->bgImagePath = public_path('images/id-card-bg.jpg');
    }

    public function generateIdCard($athletes)
    {
        // 每頁只顯示兩個運動員（左側兩個位置）
        $positions = [
            ['x' => 0, 'y' => 0],    // 左上
            ['x' => 0, 'y' => 148],  // 左下
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
                $this->generateSingleCard($athlete, $pos['x'], $pos['y']);
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

    private function addFullPageBackground()
    {
        // 添加整頁背景圖片
        if (file_exists($this->bgImagePath)) {
            $this->pdf->Image(
                $this->bgImagePath,
                0, // x
                0, // y
                210, // 寬度 - A4寬度
                297, // 高度 - A4高度
                'JPG',
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
    }

    private function generateSingleCard($athlete, $startX, $startY)
    {
        // 設置卡片區域
        $this->pdf->setPageMark();

        // 注意：這裡不再添加單獨的背景圖片，因為已經有整頁背景

        // 設置字體
        $this->pdf->SetFont('NotoSerifTC', 'B', 14);

        // 檢查是否有 name_secondary
        $hasSecondaryName = !empty($athlete->name_secondary);

        // 計算名稱區域的垂直居中位置
        $nameAreaCenterY = $hasSecondaryName ? 
            ($startY + 76 + $startY + 84) / 2 : // 有次名稱時，在兩行中間
            $startY + 80; // 只有主名稱時，在原來次名稱的位置

        // 定義字段位置和寬度 - 調整x坐標以適應左側位置
        $fields = [
            'name' => [
                'x' => $startX + 31, 
                'y' => $hasSecondaryName ? $startY + 77 : $nameAreaCenterY,
                'width' => 48
            ],
            'name_secondary' => [
                'x' => $startX + 31, 
                'y' => $startY + 84,
                'width' => 48
            ],
            'programCategoryWeight' => [
                'x' => $startX + 28, 
                'y' => $startY + 99,
                'width' => 50
            ],
            'team' => [
                'x' => $startX + 28, 
                'y' => $startY + 119,
                'width' => 50
            ]
        ];

        // 添加運動員數據
        $this->addField($fields['name'], ($this->smartTruncate($athlete->name) ?? ''));
        
        // 只有在有 name_secondary 時才顯示
        if ($hasSecondaryName) {
            $this->addField($fields['name_secondary'], ($this->smartTruncate($athlete->name_secondary) ?? ''));
        }
        
        $this->addField($fields['programCategoryWeight'], ($athlete->programCategoryWeight ?? ''));
        $this->addField($fields['team'], ($athlete->team->name ?? ''));

        // 添加照片和QR碼...
        if (!empty($athlete->photo) && is_string($athlete->photo)) {
            $this->addPhoto($athlete->photo, $startX + 65, $startY + 25, 30, 40);
        }

        if (!empty($athlete->id_number)) {
            $this->addQrCode($athlete->id_number, $startX + 70, $startY + 110, 25);
        }
    }

    private function addField($position, $text)
    {
        if (is_array($position) && isset($position['x']) && isset($position['y'])) {
            $this->pdf->SetXY($position['x'], $position['y']);
            
            // 如果有指定寬度，則使用置中對齊，否則使用左對齊
            if (isset($position['width'])) {
                $this->pdf->Cell($position['width'], 0, $this->truncateText($text, 14), 0, 1, 'C');
            } else {
                $this->pdf->Cell(0, 0, $this->truncateText($text, 13), 0, 1, 'L');
            }
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