<?php

namespace App\Services\Printer;

use TCPDF;
use Illuminate\Support\Facades\Storage;
use App\Helpers\PdfHelper;

class AthleteWeighInService
{
    private $pdf;
    private $title = "運動員過磅總表";
    private $logo_primary = 'images/mja_logo.png';
    private $title_sub = null;
    private $logo_secondary;
    private $titleFont = 'NotoSerifTC';

    private $maxWeightMap = [
        'MW33+' => '10',
        'MW42+' => '10',
        'MW46+' => '10',
        'MW50+' => '15',
        'MW60+' => '10',
        'MW66+' => '20',
        'MW73+' => '20',
        'FW33+' => '10',
        'FW40+' => '10',
        'FW44+' => '10',
        'FW48+' => '15',
        'FW57+' => '10',
        'FW63+' => '20',
    ];

    public function __construct()
    {
        $this->pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetCreator('Sports Club');
        $this->pdf->SetAuthor('Sports Club');
        $this->pdf->SetTitle('Athlete Weigh-In List');
        $this->pdf->SetMargins(15, 40, 15);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
    }

    public function generateAllWeighInTable($programs)
    {
        foreach ($programs as $index => $program) {
            $this->pdf->AddPage();
            if(!empty($this->maxWeightMap[$program->weight_code])){
                $upper_limit = ' (上限' . $this->maxWeightMap[$program->weight_code] . 'kg)';
            }else {
                $upper_limit = '';
            }

            $programData = [
                'ellipseData' => [
                    'program' => $program->converGender() . $program->competitionCategory->name,
                    'athletes_count' => $program->athletes->count(),
                    'weight' => $program->convertWeight(),
                    'upper_limit' => $upper_limit
                ]
            ];

            $helper = new PdfHelper($this->pdf);
            $helper->header3(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont, $programData['ellipseData']);

            $this->addWeighInTableWithPagination($program->athletes, $program->name, $programData);
        }

        return $this->pdf;
    }

    private function addWeighInTableWithPagination($athletes, $programName, $programData)
    {
        $columnWidths = [
            15,  // 序號
            57,  // 姓名
            57,  // 外文姓名
            45,  // 隊伍
            25,  // 過磅體重
            45,  // 簽名
            25   // 備註
        ];

        $headers = [
            '序號',
            '姓名',
            '外文姓名',
            '隊伍',
            '過磅體重 (kg)',
            '簽名',
            '備註'
        ];

        // 修改：直接設置表格起始位置
        $startY = 30; // 向下移動30
        $signatureAreaHeight = 45;
        $availableHeight = 190 - $startY - $signatureAreaHeight;
        $rowHeight = 10;
        $headerHeight = 8;
        $maxRowsPerPage = floor(($availableHeight - $headerHeight) / $rowHeight);

        $athletesArray = $athletes->toArray();
        $totalRows = count($athletesArray);
        $currentPage = 1;
        $processedRows = 0;

        while ($processedRows < $totalRows) {
            // 如果不是第一頁，添加新頁面並重新添加 header
            if ($currentPage > 1) {
                $this->pdf->AddPage();
                
                // 重新添加頁首
                $helper = new PdfHelper($this->pdf);
                $helper->header3(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont, $programData['ellipseData']);
            }

            // 重要：在繪製表格前明確設置 Y 座標
            $this->pdf->SetY($startY);

            // 添加表格表頭
            $this->addTableHeader($headers, $columnWidths, $headerHeight);

            // 計算本頁要顯示的行數
            $rowsThisPage = min($maxRowsPerPage, $totalRows - $processedRows);

            // 添加數據行
            $this->addTableRows($athletesArray, $processedRows, $rowsThisPage, $columnWidths, $rowHeight);

            // 更新已處理的行數
            $processedRows += $rowsThisPage;

            // 在當前頁面底部添加簽名格
            $this->addSignatures();

            $currentPage++;
        }
    }

    private function addTableHeader($headers, $columnWidths, $headerHeight)
    {
        // 確保從左邊界開始
        $this->pdf->SetX(15);
        
        $this->pdf->SetFillColor(200, 200, 200);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont('NotoSerifTC', 'B', 10);

        foreach ($headers as $key => $header) {
            $this->pdf->Cell($columnWidths[$key], $headerHeight, $header, 1, 0, 'C', true);
        }
        $this->pdf->Ln();
        
        // 確保換行後從左邊界開始
        $this->pdf->SetX(15);
    }

    private function addTableRows($athletesArray, $startIndex, $rowsCount, $columnWidths, $rowHeight)
    {
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont('NotoSerifTC', '', 12);

        for ($i = 0; $i < $rowsCount; $i++) {
            $athlete = $athletesArray[$startIndex + $i];
            
            // 檢查是否會超出頁面（考慮簽名區域）
            if ($this->pdf->GetY() + $rowHeight > 220 - 45) {
                break;
            }

            $rowData = [
                $startIndex + $i + 1,
                $this->smartTruncate($athlete['name'] ?? '', 20),
                $this->smartTruncate($athlete['name_secondary'] ?? '', 20),
                $this->truncateText($athlete['team']['name'] ?? '', 15),
                '', // 過磅體重（留空）
                '', // 簽名（留空）
                ''  // 備註（留空）
            ];

            // 確保從左邊界開始
            $this->pdf->SetX(15);
            
            foreach ($rowData as $key => $data) {
                $this->pdf->Cell($columnWidths[$key], $rowHeight, $data, 1, 0, 'C');
            }
            $this->pdf->Ln();
        }
    }

        private function addSignatures()
    {
        // 直接設置簽名區域的 Y 座標
        $this->pdf->SetY(175);
        
        $signatureWidth = 80;
        $lineHeight = 6;
        
        // 設置左邊界
        $this->pdf->SetX(15);
        
        $this->pdf->SetFont('NotoSerifTC', 'B', 10);
        $this->pdf->Cell($signatureWidth, $lineHeight, '監磅員簽名', 0, 0, 'L');
        
        $this->pdf->SetX(105);
        $this->pdf->Cell($signatureWidth, $lineHeight, '監磅員簽名', 0, 0, 'L');
        
        $this->pdf->SetX(190);
        $this->pdf->Cell($signatureWidth, $lineHeight, '賽事負責人', 0, 1, 'L');
        
        $this->pdf->Ln(14);
        
        // 簽名線
        $lineY = $this->pdf->GetY();
        $this->pdf->Line(15, $lineY, 85, $lineY);
        $this->pdf->Line(105, $lineY, 175, $lineY);
        $this->pdf->Line(190, $lineY, 260, $lineY);
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