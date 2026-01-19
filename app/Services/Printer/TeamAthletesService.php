<?php

namespace App\Services\Printer;

use TCPDF;
use App\Helpers\PdfHelper;
use App\Models\Program;

class TeamAthletesService
{
    private $pdf;
    private $title = "隊伍運動員名單";
    private $logo_primary = 'images/mja_logo.png';
    private $title_sub = null;
    private $logo_secondary;
    private $titleFont = 'NotoSerifTC';
    private $competitionName;

    public function __construct()
    {
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetCreator('Sports Management System');
        $this->pdf->SetAuthor('Sports Management System');
        $this->pdf->SetTitle('Team Athletes List');
        $this->pdf->SetMargins(15, 40, 15);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
    }

    public function generateAllTeamsAthletes($competition, $teams)
    {
        $this->competitionName = $competition->name;
        
        foreach ($teams as $teamIndex => $team) {
            $this->pdf->AddPage();
            
            // 準備橢圓形數據
            $teamData = [
                'ellipseData' => [
                    'team_name' => $team->name,
                    'athletes_count' => $team->athletes->count(),
                    'total_pages' => $this->calculateTotalPages($team->athletes),
                    'current_page' => 1
                ]
            ];

            // 使用現有的 header3 方法
            $helper = new PdfHelper($this->pdf);
            $helper->header4(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont);

            // 添加隊伍詳細資訊
            $this->addTeamInfo($team);
            
            $this->addAthletesTableWithPagination($team->athletes->sortBy('gedner'), $teamData);
            
            $this->addSignatures();
        }

        return $this->pdf;
    }

    private function calculateTotalPages($athletes)
    {
        $totalRows = $athletes->count();
        $rowsPerPage = $this->getMaxRowsPerPage();
        return ceil($totalRows / $rowsPerPage);
    }

    private function getMaxRowsPerPage()
    {
        // 計算每頁最大行數
        $startY = 70; // header 後開始位置
        $pageHeight = 297; // A4 高度
        $bottomMargin = 30; // 頁尾空間
        $rowHeight = 10;
        $headerHeight = 10;
        
        return floor(($pageHeight - $startY - $bottomMargin - $headerHeight) / $rowHeight);
    }

    private function addTeamInfo($team)
    {
        $this->pdf->SetY(30);
        $this->pdf->SetFont('NotoSerifTC', 'B', 14);
        
        // 隊伍標題在中央
        $this->pdf->Cell(0, 10, $team->name . ' 運動員名單', 0, 1, 'C');
        
        $this->pdf->Ln(2);
        
        // 隊伍資訊在右側
        $this->pdf->SetFont('NotoSerifTC', '', 12);
        $totalText = $team->name . '(' . $team->athletes->count() . '人)';
        $textWidth = $this->pdf->GetStringWidth($totalText);
        $rightX = $this->pdf->getPageWidth() - $this->pdf->getMargins()['right'] - $textWidth;
        
        $this->pdf->SetX($rightX);
        $this->pdf->Cell($textWidth, 8, $totalText, 0, 1, 'R');
        
        $this->pdf->Ln(5);
    }

    private function addAthletesTableWithPagination($athletes, $teamData)
    {
        $columnWidths = [
            15,  // 序號
            100,  // 姓名
            65   // 組別
        ];

        $headers = [
            '序號',
            '姓名',
            '組別'
        ];

        $startY = 55;
        $rowHeight = 10;
        $headerHeight = 8;
        $maxRowsPerPage = $this->getMaxRowsPerPage();

        $athletesArray = $athletes->toArray();
        $totalRows = count($athletesArray);
        
        // 使用全域的已處理行計數器
        $processedRows = 0;
        $currentPage = 1;

        while ($processedRows < $totalRows) {
            // 如果不是第一頁，添加新頁面並重新添加 header
            if ($currentPage > 1) {
                $this->pdf->AddPage();
                
                // 更新頁碼
                $teamData['ellipseData']['current_page'] = $currentPage;
                
                // 重新添加頁首
                $helper = new PdfHelper($this->pdf);
                $helper->header5(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont);
                
                // 在新頁面添加隊伍標題
                $this->pdf->SetY(30);
                $this->pdf->SetFont('NotoSerifTC', 'B', 14);
                $this->pdf->Cell(0, 10, $teamData['ellipseData']['team_name'] . ' 運動員名單 (續)', 0, 1, 'C');
                
                $this->pdf->SetY(45);
            }

            // 添加表格表頭
            $this->addTableHeader($headers, $columnWidths, $headerHeight, $startY);

            // 計算本頁要顯示的行數
            
            $rowsThisPage = min($maxRowsPerPage, $totalRows - $processedRows);

            // 添加數據行 - 傳入正確的起始索引
            $this->addTableRows($athletesArray, $processedRows, $rowsThisPage, $columnWidths, $rowHeight, $processedRows + 1);

            // 更新已處理的行數
            $processedRows += $rowsThisPage;
            $currentPage++;
        }
    }

    private function addTableHeader($headers, $columnWidths, $headerHeight, $startY = null)
    {
        if ($startY) {
            $this->pdf->SetY($startY);
        }
        
        // 確保從左邊界開始
        $this->pdf->SetX(15);
        
        $this->pdf->SetFont('NotoSerifTC', 'B', 10);
        
        // 設置白色背景
        $this->pdf->SetFillColor(255, 255, 255);
        
        // 只畫底線的邊框參數：LTRB (Left, Top, Right, Bottom)
        $border = ['L' => false, 'T' => false, 'R' => false, 'B' => true];

        foreach ($headers as $key => $header) {
            // 只保留底線，白色背景
            $this->pdf->Cell($columnWidths[$key], $headerHeight, $header, $border, 0, 'C', true);
        }
        $this->pdf->Ln();
        
        // 確保換行後從左邊界開始
        $this->pdf->SetX(15);
    }

    private function addTableRows($athletesArray, $startIndex, $rowsCount, $columnWidths, $rowHeight, $startingNumber = 1)
    {
        $this->pdf->SetFont('sourcehanserifhcvf', '', 10);
        
        // 設置白色背景
        $this->pdf->SetFillColor(255, 255, 255);
        
        // 只畫底線的邊框參數
        $border = ['L' => false, 'T' => false, 'R' => false, 'B' => true];

        for ($i = 0; $i < $rowsCount; $i++) {
            $athlete = $athletesArray[$startIndex + $i];

            // 獲取組別信息
            $groupText = $this->getAthleteGroups($athlete);

            // 組成名稱：中文名 + 外文名
            $chineseName = $athlete['name'] ?? '';
            $secondaryName = $athlete['name_secondary'] ?? '';
            
            $fullName = $chineseName;
            if (!empty($secondaryName)) {
                $fullName .= $secondaryName;
            }
            
            if (empty($fullName)) {
                $fullName = '未命名';
            }

            // 使用傳入的 startingNumber 加上 i 來計算正確的序號
            $rowData = [
                $startingNumber + $i,  // 修正這裡
                $fullName,
                $groupText
            ];

            // 確保從左邊界開始
            $this->pdf->SetX(15);
            
            foreach ($rowData as $key => $data) {
                $alignment = ($key === 0) ? 'C' : (($key === 1) ? 'L' : 'L');
                // 只保留底線，白色背景
                $this->pdf->Cell($columnWidths[$key], $rowHeight, $data, $border, 0, $alignment, true);
            }
            $this->pdf->Ln();
            
            // 檢查是否需要換頁
            if ($this->pdf->GetY() + $rowHeight > 250) {
                break;
            }
        }
    }

    private function getAthleteGroups($athlete)
    {
        $groups = [];
        foreach ($athlete['programs'] as $p) {
            $program = Program::where('id',$p['id'])->first();
            $groups[] = $program->converGender() . $program->category->name . $program->convertWeight();
        }
        
        $groups = array_unique($groups);
        return !empty($groups) ? implode(', ', $groups) : '未分組';
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

    public function setCompetitionName($name)
    {
        $this->competitionName = $name;
        return $this;
    }

    public function outputPDF($filename = 'teams_athletes.pdf', $outputType = 'I')
    {
        return $this->pdf->Output($filename, $outputType);
    }

    private function addSignatures()
    {
        // 直向A4的寬度約為210mm，高度約為297mm
        // 簽名區域設置在底部
        
        // 設置簽名區域的Y座標（從底部往上計算）
        $pageHeight = 297; // A4高度
        $signatureAreaHeight = 40; // 簽名區域高度
        $startY = $pageHeight - $signatureAreaHeight;
        
        $this->pdf->SetY($startY);
        
        $signatureWidth = 45; // 直向寬度較小
        $lineHeight = 6;
        $marginLeft = 15; // 左邊距
        
        // 第一行：標題
        $this->pdf->SetX($marginLeft);
        $this->pdf->SetFont('NotoSerifTC', 'B', 10);
        
        // 三個簽名欄位均勻分布
        $totalWidth = 180; // 210 - 15*2 = 180mm可用寬度
        $columnSpacing = $totalWidth / 3;
        
        // 第一個簽名
        $this->pdf->Cell($signatureWidth, $lineHeight, '屬會/學校負責⼈名稱', 0, 0, 'C');
        
        // 第二個簽名
        $this->pdf->SetX($marginLeft + $columnSpacing);
        $this->pdf->Cell($signatureWidth, $lineHeight, '屬會/學校負責⼈簽名', 0, 0, 'C');
        
        // 第三個簽名
        $this->pdf->SetX($marginLeft + $columnSpacing + 47);
        $this->pdf->Cell($signatureWidth, $lineHeight, '⽇期', 0, 1, 'C');
        
        // 換行，增加間距
        $this->pdf->Ln(15);
        
        // 簽名線
        $lineY = $this->pdf->GetY();
        $lineWidth = 50; // 簽名線長度
        
        // 第一條線
        $line1X = $marginLeft + ($columnSpacing - $lineWidth) / 2;
        $this->pdf->Line($line1X, $lineY, $line1X + $lineWidth, $lineY);
        
        // 第二條線
        $line2X = $marginLeft + $columnSpacing + ($columnSpacing - $lineWidth) / 2;
        $this->pdf->Line($line2X, $lineY, $line2X + $lineWidth, $lineY);
        
        // 第三條線
        $line3X = $marginLeft + ($columnSpacing * 2) + ($columnSpacing - $lineWidth) / 2;
        $this->pdf->Line($line3X, $lineY, $line3X + $lineWidth, $lineY);
    }
}