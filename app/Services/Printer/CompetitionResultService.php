<?php

namespace App\Services\Printer;

use TCPDF;
use Illuminate\Support\Facades\Storage;
use App\Helpers\PdfHelper;

class CompetitionResultService
{
    private $pdf;
    private $title = "賽事結果總表";
    private $logo_primary = 'images/mja_logo.png';
    private $title_sub = null;
    private $logo_secondary;
    private $titleFont = 'NotoSerifTC';
    private $blankMedals = false; // 新增參數控制是否空白

    public function __construct()
    {
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetCreator('Sports Club');
        $this->pdf->SetAuthor('Sports Club');
        $this->pdf->SetTitle('Competition Results');
        $this->pdf->SetMargins(15, 40, 15);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
    }

    public function generateAllResultTableByCategory($programsByCategory, $blankMedals = false)
    {
        $this->blankMedals = $blankMedals; // 設置參數
        
        foreach ($programsByCategory as $categoryId => $programs) {
            $category = $programs->first()->competitionCategory;

            $this->pdf->AddPage();

            $helper = new PdfHelper($this->pdf);
            $helper->header4(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont);

            $this->addResultTableByCategory($programs, $category);
        }

        return $this->pdf;
    }

    private function addResultTableByCategory($programs, $category)
    {
        $startY = 30;
        
        // 分開男女項目 - 使用 weight_code 的首個字
        $malePrograms = $programs->filter(function($program) {
            return !empty($program->weight_code) && strtoupper(substr($program->weight_code, 0, 1)) === 'M';
        });
        
        $femalePrograms = $programs->filter(function($program) {
            return !empty($program->weight_code) && strtoupper(substr($program->weight_code, 0, 1)) === 'F';
        });

        $currentY = $startY;

        // 顯示分類標題
        $this->pdf->SetY($currentY);
        $this->pdf->SetFont('NotoSerifTC', 'B', 16);
        $this->pdf->Cell(0, 10, $category->name, 0, 1, 'C');
        $currentY = $this->pdf->GetY() + 5;

        // 顯示男子項目
        if ($malePrograms->count() > 0) {
            $this->pdf->SetY($currentY);
            $this->pdf->SetFont('NotoSerifTC', 'B', 14);
            $this->pdf->Cell(0, 8, '男子組', 0, 1, 'L');
            $currentY = $this->pdf->GetY();
            
            $currentY = $this->addCategoryTable($malePrograms, $currentY, '男子組');
            $currentY += 10;
        }

        // 檢查女子組是否能在當前頁面完整顯示
        if ($femalePrograms->count() > 0) {
            $estimatedFemaleHeight = $this->estimateTableHeight($femalePrograms);
            
            // 如果女子組無法在當前頁面完整顯示，則換新頁
            if ($currentY + $estimatedFemaleHeight > 240) {
                $this->pdf->AddPage();
                $currentY = 40;
                
                // 重新添加頁首
                $helper = new PdfHelper($this->pdf);
                $helper->header1(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont);
                
                // 重新添加分類標題
                $this->pdf->SetY($currentY);
                $this->pdf->SetFont('NotoSerifTC', 'B', 16);
                $this->pdf->Cell(0, 10, $category->name . ' (續)', 0, 1, 'C');
                $currentY = $this->pdf->GetY() + 5;
            }
            
            $this->pdf->SetY($currentY);
            $this->pdf->SetFont('NotoSerifTC', 'B', 14);
            $this->pdf->Cell(0, 8, '女子組', 0, 1, 'L');
            $currentY = $this->pdf->GetY();
            
            $this->addCategoryTable($femalePrograms, $currentY, '女子組');
        }
    }

    private function estimateTableHeight($programs)
    {
        $rowHeight = 17;
        $headerHeight = 8;
        $titleHeight = 8; // 組別標題高度
        
        return $titleHeight + $headerHeight + (count($programs) * $rowHeight);
    }

    private function addCategoryTable($programs, $startY, $groupName)
    {
        // 調整欄位寬度，金銀銅格變窄
        $columnWidths = [30, 38, 38, 38, 38]; // 公斤級, 金牌, 銀牌, 銅牌, 銅牌
        $rowHeight = 17; // 減少行高
        $headerHeight = 8;

        $headers = ['公斤級', '冠軍', '亞軍', '季軍', '季軍'];

        // 設置表格起始位置
        $this->pdf->SetY($startY);

        // 添加表格表頭
        $this->addTableHeader($headers, $columnWidths, $headerHeight);

        $currentY = $this->pdf->GetY();
        
        foreach ($programs as $program) {
            // 檢查是否需要換頁
            if ($currentY + $rowHeight > 240) {
                $this->pdf->AddPage();
                $currentY = 40;
                
                // 重新添加頁首
                $helper = new PdfHelper($this->pdf);
                $helper->header1(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont);
                
                // 重新添加分類標題和組別標題
                $this->pdf->SetY($currentY);
                $this->pdf->SetFont('NotoSerifTC', 'B', 16);
                $this->pdf->Cell(0, 10, $category->name . ' (續)', 0, 1, 'C');
                $currentY = $this->pdf->GetY() + 5;
                
                $this->pdf->SetY($currentY);
                $this->pdf->SetFont('NotoSerifTC', 'B', 14);
                $this->pdf->Cell(0, 8, $groupName . ' (續)', 0, 1, 'L');
                $currentY = $this->pdf->GetY();
                
                $this->addTableHeader($headers, $columnWidths, $headerHeight);
                $currentY = $this->pdf->GetY();
            }

            $this->addProgramRow($program, $columnWidths, $rowHeight, $currentY);
            $currentY += $rowHeight;
            $this->pdf->SetY($currentY);
        }

        return $currentY;
    }

    private function addTableHeader($headers, $columnWidths, $height)
    {
        $this->pdf->SetX(15);
        $this->pdf->SetFillColor(200, 200, 200);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont('NotoSerifTC', 'B', 10);

        foreach ($headers as $key => $header) {
            $this->pdf->Cell($columnWidths[$key], $height, $header, 1, 0, 'C', true);
        }
        $this->pdf->Ln();
        $this->pdf->SetX(15);
    }

    private function addProgramRow($program, $columnWidths, $rowHeight, $startY)
    {
        $athletes = $program->athletes;
        $weight = $program->convertWeight() ?? $program->weight_code;
        
        $this->pdf->SetY($startY);
        $this->pdf->SetX(15);
        
        // 第一格：公斤級 - 加大字體
        $this->pdf->SetFont('NotoSerifTC', 'B', 12); // 從 10 加大到 12
        $this->pdf->Cell($columnWidths[0], $rowHeight, $weight, 1, 0, 'C');
        
        // 金牌格
        $this->addMedalCell($columnWidths[1], $rowHeight, isset($athletes[0]) ? $athletes[0] : null, '金牌');
        
        // 銀牌格
        $this->addMedalCell($columnWidths[2], $rowHeight, isset($athletes[1]) ? $athletes[1] : null, '銀牌');
        
        // 銅牌格
        $this->addMedalCell($columnWidths[3], $rowHeight, isset($athletes[2]) ? $athletes[2] : null, '銅牌');
        
        // 銅牌格
        $this->addMedalCell($columnWidths[4], $rowHeight, isset($athletes[3]) ? $athletes[3] : null, '銅牌');
        
        $this->pdf->Ln();
    }

    private function addMedalCell($width, $height, $athlete, $medalType)
    {
        if ($this->blankMedals) {
            // 如果設置為空白模式，直接繪製空白格子
            $this->pdf->Cell($width, $height, '', 1, 0, 'C');
        } else {
            if ($athlete) {
                // 保存當前位置
                $startX = $this->pdf->GetX();
                $startY = $this->pdf->GetY();
                
                // 繪製外框
                $this->pdf->Cell($width, $height, '', 1, 0, 'C');
                
                // 計算內部內容位置 - 進一步減少間隙
                $lineHeight = 5; // 減少行高
                $totalHeight = $lineHeight * 3;
                $startOffset = ($height - $totalHeight) / 2; // 垂直居中
                
                // 選手姓名 - 增大字體，減少間隙
                $this->pdf->SetXY($startX, $startY + $startOffset);
                $this->pdf->SetFont('NotoSerifTC', 'B', 10); // 增大字體
                $this->pdf->Cell($width, $lineHeight, $this->truncateText($athlete->name, 10), 0, 0, 'C');
                
                // 外文姓名 - 增大字體，減少間隙
                $this->pdf->SetXY($startX, $startY + $startOffset + $lineHeight);
                $this->pdf->SetFont('NotoSerifTC', '', 9); // 增大字體
                $this->pdf->Cell($width, $lineHeight, $this->smartTruncate($athlete->name_secondary ?? '', 15), 0, 0, 'C');
                
                // 隊伍 - 增大字體，減少間隙
                $this->pdf->SetXY($startX, $startY + $startOffset + ($lineHeight * 2));
                $this->pdf->SetFont('NotoSerifTC', '', 9); // 增大字體
                $this->pdf->Cell($width, $lineHeight, $this->truncateText($athlete->team->name ?? '', 12), 0, 0, 'C');
                
                // 恢復到正確位置繼續下一格
                $this->pdf->SetXY($startX + $width, $startY);
            } else {
                // 沒有選手，用灰色蓋住整個格
                $this->pdf->SetFillColor(200, 200, 200);
                $this->pdf->Cell($width, $height, '', 1, 0, 'C', true);
                
                // 顯示"空缺"文字
                $this->pdf->SetTextColor(100, 100, 100);
                $this->pdf->SetFont('NotoSerifTC', '', 9); // 增大字體
                
                // 在灰色格子上顯示文字
                $startX = $this->pdf->GetX() - $width;
                $startY = $this->pdf->GetY();
                $this->pdf->SetXY($startX, $startY + ($height / 2) - 2);
                $this->pdf->Cell($width, 4, '空缺', 0, 0, 'C');
                
                // 恢復位置和顏色
                $this->pdf->SetXY($startX + $width, $startY);
                $this->pdf->SetTextColor(0, 0, 0);
            }
        }
    }

    private function truncateText($text, $maxLength)
    {
        if (empty($text)) {
            return '-';
        }
        
        if (mb_strlen($text) > $maxLength) {
            return mb_substr($text, 0, $maxLength) . '...';
        }
        return $text;
    }

    private function smartTruncate($name, $maxLength = 12)
    {
        if (empty($name)) {
            return '-';
        }
        
        if (mb_strlen($name) <= $maxLength) {
            return $name;
        }
        
        $parts = explode(' ', $name);
        
        if (count($parts) >= 2) {
            $shortName = '';
            for ($i = 0; $i < count($parts) - 1; $i++) {
                if (!empty($parts[$i])) {
                    $shortName .= mb_substr($parts[$i], 0, 1) . '.';
                }
            }
            
            $shortName .= ' ' . end($parts);
            
            if (mb_strlen($shortName) <= $maxLength) {
                return $shortName;
            }
            
            $firstName = mb_substr($parts[0], 0, 1) . '.';
            $lastName = end($parts);
            $simplestName = $firstName . ' ' . $lastName;
            
            if (mb_strlen($simplestName) <= $maxLength) {
                return $simplestName;
            }
        }
        
        return mb_substr($name, 0, $maxLength - 3) . '...';
    }

    public function setTitle($title, $subtitle = null)
    {
        $this->title = $title;
        if ($subtitle) {
            $this->title_sub = $subtitle;
        }
        return $this;
    }

    public function setLogos($primaryLogo, $secondaryLogo)
    {
        $this->logo_primary = $primaryLogo;
        $this->logo_secondary = $secondaryLogo;
        return $this;
    }

    public function setTitleFont($font)
    {
        $this->titleFont = $font;
        return $this;
    }
}