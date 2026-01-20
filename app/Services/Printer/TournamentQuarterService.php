<?php

namespace App\Services\Printer;

use App\Helpers\PdfHelper;
use TCPDF;

class TournamentQuarterService
{
    protected $gameSetting;
    protected $pdf = null;
    protected $title = 'Judo Competition of Asia Pacific';
    protected $title_sub = 'Judo Union of Asia';
    protected $logo_primary = '';
    protected $logo_secondary = null;

    protected $startX = 25; //面頁基點X軸
    protected $startY = 23; //面頁基點Y軸
    protected $boxW = 45; //運動員名牌高度
    protected $boxH = 10; //運動員名牌寛度
    protected $boxGap = 2; //運動員名牌之間距離
    protected $athleteGap = 2.5;
    protected $arcW = 20; //上線曲線寛度
    protected $arcWFirst = 4; //第一輪上線曲線
    protected $repechageDistance = 5; //復活賽表,運動員名牌之間距離
    protected $repechageBoxGap = 2; //復活賽表,運動員名牌之間距離
    protected $repechageSectionGap = 5; //復活賽表, second
    protected $circleSize = 3;
    protected $circleFontSize = 10;
    protected $playerFontSize = 8;
    protected $repechageBoxH = 1;

    protected $round = 0;
    protected $playerCount = 0;
    protected $winnerLineDraw = false;

    protected $titleFont = 'times';
    protected $playerFont = 'times';
    protected $generalFont = 'times';

    protected $arcColor = array(50, 50, 127);
    protected $boxWhiteColor = array(255, 255, 255);
    protected $boxBlueColor = array(255, 255, 255);
    protected $circleColor = array(240, 240, 240);
    protected $styleWinnerLine = null;
    protected $styleArcLine = null;
    protected $styleBoxLine = null;
    protected $styleResult1 = null;
    protected $styleResult2 = null;
    protected $styleCircle = null;
    protected $poolLabel = [
        ['name' => 'Pool A', 'color' => array(243, 151, 0)],
        ['name' => 'Pool B', 'color' => array(230, 39, 37)],
        ['name' => 'Pool C', 'color' => array(122, 184, 42)],
        ['name' => 'Pool D', 'color' => array(98, 197, 230)]
    ];
    protected $winners = [];
    protected $sequences = [];
    protected $resultColor1 = array(200, 200, 200);
    protected $resultColor2 = array(255, 255, 255);
    protected $resultXY = [120, 120];

    public function __construct($settings)
    {
        $this->gameSetting = $settings;
        $this->styleWinnerLine = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $this->styleArcLine = array('width' => 0.10, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50, 127));
        $this->styleBoxLine = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $this->styleResult1 = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleResult2 = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleCircle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50, 127));
    }
    public function setPoolLabel($poolLabel = null)
    {
        if ($poolLabel == null) {
            $this->poolLabel = null;
            return true;
        }
        foreach ($poolLabel as $i => $pool) {
            if (isset($pool['name'])) {
                $this->poolLabel[$i]['name'] = $pool['name'];
            }
            if (isset($pool['color'])) {
                $this->poolLabel[$i]['color'] = $pool['color'];
            }
        }
    }
    public function setLogos($primary = null, $secondary = null)
    {
        $this->logo_primary = $primary;
        $this->logo_secondary = $secondary;
    }

    public function setTitles($title = null, $title_sub = null)
    {
        $this->title = $title;
        $this->title_sub = $title_sub;
    }
    public function setFonts($titleFont = 'times', $playerFont = 'times', $generalFont = 'times')
    {
        $this->titleFont = $titleFont;
        $this->playerFont = $playerFont;
        $this->generalFont = $generalFont;
    }
    public function setWinnerLine($winnerLineDraw)
    {
        $this->winnerLineDraw = $winnerLineDraw;
    }

    public function pdf($players = [], $winners = [],  $sequences = [], $winnerList = [], $ellipseData = [], $repechagePlayers = [], $repechage = true)
    {
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // 設定邊距和其他屬性
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetMargins(15, 10, 15);
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->AddPage();
        
        $this->playerCount = count($players) * 2;
        
        // 載入遊戲設定
        foreach ($this->gameSetting[$this->playerCount] as $key => $value) {
            $this->$key = $value;
        }
        
        $this->round = strlen((string)decbin($this->playerCount / 2));
        
        // 處理 winners 參數
        if (empty($winners) && isset($this->winners)) {
            $winners = $this->winners;
        } else {
            $winners = is_array($winners) ? $winners : [];
        }
        
        // 處理 sequences 參數
        
        if (empty($sequences) && isset($this->sequences)) {
            $sequences = $this->sequences;
        } else {
            $sequences = is_array($sequences) ? $sequences : [];
        }
        
        $helper = new PdfHelper($this->pdf);

        $helper->header1(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont, $ellipseData);
        $this->mainChart($players, $sequences, $winners); // 主上線表包括運動員名牌和上線曲線
        
        if ($this->playerCount > 4 && $this->poolLabel != null) {
            $this->boxPool($this->poolLabel);
        }
        
        if ($repechagePlayers) {
            // dd($repechagePlayers);
            $this->repechageChart(count($players), $repechagePlayers, [[1,2],[3,4]], $winners); // 復活賽上線表包括運動員名牌和上線曲線
        }
        
        $this->resultBox($winnerList);
        $this->pdf->Output($ellipseData['program'] . $ellipseData['weight'] . '上線表.pdf', 'I');
    }

    public function pdfForMultiplePrograms($programsData)
    {
        // 初始化 PDF
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetMargins(15, 10, 15);
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        
        foreach ($programsData as $index => $programData) {
            // dd($programData);
            if ($index >= 0) {
                $this->pdf->AddPage();
            }
            
            $this->playerCount = count($programData['players']) * 2;
            
            // 載入遊戲設定
            foreach ($this->gameSetting[$this->playerCount] as $key => $value) {
                $this->$key = $value;
            }
            
            $this->round = strlen((string)decbin($this->playerCount / 2));
            
            $helper = new PdfHelper($this->pdf);
            $helper->header1(12, 4, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont, $programData['ellipseData']);

            $this->mainChart($programData['players'], $this->sequences, $programData['winners']);
            
            if ($this->playerCount > 4 && $this->poolLabel != null) {
                $this->boxPool($this->poolLabel);
            }
            
            if (!empty($programData['repechagePlayers'])) {
                $this->repechageChart(count($programData['players']), $programData['repechagePlayers'], [[1,2],[3,4]], $programData['winners'] ?? []);
            }
            // dd($this->sequences);
            $this->resultBox($programData['winnerList']);
        }
        
        $this->pdf->Output($this->title . '所有上線表.pdf', 'I');
    }

    private function boxPool($poolLabel)
    {
        $this->pdf->setFont($this->generalFont, '', $this->playerFontSize);
        $x = $this->startX - 10;
        $y = $this->startY;
        $h = ($this->boxH + $this->boxGap) * ($this->playerCount / 8) - $this->boxGap;
        
        for ($i = 3; $i >= 0; $i--) {
            $this->pdf->RoundedRect($x, $y, 6.5, $h, 3.25, '1111', 'DF', $this->styleCircle, $poolLabel[$i]['color']);
            $y += ($this->boxH + $this->boxGap) * ($this->playerCount / 8);
        }

        $y = $this->startY + (($this->boxH + $this->boxGap) * $this->playerCount / 2) - ($this->boxGap / 2);
        $w = (($this->boxH + $this->boxGap) * $this->playerCount / 8);
        
        $this->pdf->StartTransform();
        $this->pdf->setXY($x, $y);
        $this->pdf->Rotate(90);
        
        for ($i = 3; $i >= 0; $i--) {
            $name = $poolLabel[$i]['name'];
            
            // 使用精確的文字寬度計算
            $fontSize = $this->calculateOptimalFontSize($name, $this->playerFont, $w, $this->playerFontSize);
            
            $this->pdf->setFont($this->playerFont, '', $fontSize);
            $this->pdf->Cell($w, 0, $name, 0, 0, 'C', 0, '');
        }
        
        $this->pdf->StopTransform();
    }

    /**
     * 計算最適合的字型大小
     * 
     * @param string $text 文字內容
     * @param string $font 字型名稱
     * @param float $maxWidth 最大允許寬度
     * @param int $preferredSize 首選字型大小
     * @return int 最佳字型大小
     */
    private function calculateOptimalFontSize($text, $font, $maxWidth, $preferredSize)
    {
        // 設定初始字型大小
        $fontSize = $preferredSize;
        
        // 檢查文字寬度
        $this->pdf->setFont($font, '', $fontSize);
        $textWidth = $this->pdf->GetStringWidth($text);
        
        // 如果文字太寬，逐步減小字型大小
        while ($textWidth > $maxWidth * 0.9 && $fontSize > 6) {
            $fontSize--;
            $this->pdf->setFont($font, '', $fontSize);
            $textWidth = $this->pdf->GetStringWidth($text);
        }
        
        return $fontSize;
    }

    private function mainChart($players, $sequences, $winners)
    {
        /* 運動員名牌 */
        $boxX = $this->startX; //box X axis, do prevent the change of startX original value,
        $boxY = $this->startY; //box Y axis, do prevent the change of the startY original value,
        $arcWFirst = $this->arcWFirst;
        /* player box */
        for ($i = 0; $i < $this->playerCount / 2; $i++) {
            $this->boxPlayers($boxX, $boxY, $this->boxW, $this->boxH, $players[$i]);
            $boxY += $this->boxGap + $this->boxH; //accumulated for the text box starting point
        }

        /* 第一輪上線曲線 */
        $px = $this->startX + $this->boxW; //starting point of $x axis
        $py = $this->startY + ($this->boxH / 4); //starting point of $y axis
        $pW = $this->arcWFirst; //width of arch shape
        $h = $this->boxH / 2; //height of arch shape
        $pGap = $h * 2 + $this->boxGap; //$h +$boxH+$boxGap; //gap betwee each arch in vertical alignment
        for ($i = 0; $i < $this->playerCount / 2; $i++) {
            $this->arcLine($px, $py, $pW, $h, $sequences[0][$i], $winners[0][$i] ?? 0, true);
            $py += $pGap;
        }

        /* 第一輪以外的上線曲線  */
        $ax = $this->startX + $this->boxW + $arcWFirst;
        $ah = $this->boxH + $this->boxGap;
        $ay = $this->startY + ($this->boxH / 2);
        $cnt = $this->playerCount / 4;
        for ($i = 1; $i <= $this->round - 1; $i++) {
            $arcGap = $ah * 2; //gap betwee each arch in vertical alignment
            for ($j = 0; $j < $cnt; $j++) {
                $this->arcLine($ax, $ay, $this->arcW, $ah, $sequences[$i][$j], $winners[$i][$j] ?? 0);
                $ay += $arcGap;
            }
            $ax += $this->arcW;
            $ay = $this->startY + $ah - ($this->boxGap / 2);
            $ah += $ah;
            $cnt /= 2;
        }
        // if($ax+$this->arcW > 200){
        //     $this->pdf->line($ax, $ay, $ax-$this->arcW, $ay);
        // }else{
        //     $this->pdf->line($ax, $ay, $ax+$this->arcW, $ay);

        // }
    }
    private function repechageChart($totalPlayers, $players, $sequences, $winners)
    {
        // dd($sequences);
        $x = $this->startX;
        $y = $this->startY + (($this->boxH + $this->boxGap) * $totalPlayers) - $this->boxGap + $this->repechageDistance;
        $boxGap = $this->repechageBoxGap;
        $boxH = $this->repechageBoxH;
        $boxW = $this->boxW;
        $arcW = $this->arcW;

        $this->pdf->Line($x, $y, $x + 110, $y, $this->styleResult1); //Repechage horizontal sperate line
        $this->pdf->RoundedRect($x + $boxW + $this->arcWFirst + 5, $y - 2, 30, 6, 2, '1111', 'F', $this->styleBoxLine, array(255, 255, 255));
        $this->pdf->setXY($x + $boxW + $this->arcWFirst + 5, $y - 2);
        $this->pdf->setFont($this->titleFont, '', $this->playerFontSize);
        $this->pdf->Cell(30, 4, '復活賽', 0, 1, 'C', 0, '', 0);

        $y += $this->repechageDistance;
        $x1 = $this->startX;
        $y1 = $y;
        $round = count($players);
        // dd($sequences);
        $x = $x1;
        $y = $y;
        $x1 = $x;
        $y1 = $y;
        $h = $boxH;
        $w = $boxW;
        for ($i = 0; $i < $round; $i++) {
            if ($round - 2 == $i) {
                $h += $h;
                $ty = $y1; //暫存倒數第二個輪上線的Y1位置,用以計算最後一個的位置
            }
            if ($round - 1 == $i) {
                $y1 = $ty + $h / 2;
            }
            for ($j = 0; $j < count($players[$i]); $j++) {
                // dd($sequences[$i+$round+1][$j]);
                // dd($winners[$i+$round+1]);
                //$this->arcLine($x1, $y1, $w, $h, $sequences[$i+$round+1][$j], $winners[$i+$round+1][$j],$i==0, $i==0?null:$players[$i][$j]);
                // dd($sequences);
                $this->arcLine($x1, $y1, $w, $h, $sequences[$i][$j], 1, $i == 0, $i == 0 ? null : $players[$i][$j]);
                if ($players[$i][$j]) {
                    if (isset($players[$i][$j]['white'])) {
                        $this->pdf->text($x1 - 6, $y1 - ($boxH / 2) - 0.25, $players[$i][$j]['white']['from']);
                    }
                    $this->pdf->text($x1 - 6, $y1 + $h - ($boxH / 2) - 0.25, $players[$i][$j]['blue']['from']);
                }
                $y1 += $h * 2;
            }
            $x1 += $w;
            $y1 = $y + $boxH / 2 + (($h / 2) * $i);
            $w = $arcW;
        }
    }
    /* the following are repeated object, might change the variabled values, accordingly, not suggest to use globale variabled. */
    private function boxPlayers($x, $y, $w, $h, $players = ['white' => ['name' => 'white' , 'name_secondary' => '1'], 'blue' => ['name' => 'blue' , 'name_secondary' => '1']])
    {
        $this->pdf->setFont($this->playerFont, '', $this->playerFontSize);
        $r = 2.0;
        $h = $h / 2;
        
        // 定義漸變顏色 - 使用銀白色調
        $silver = [220, 220, 220];  // 較亮的銀色
        $white = [255, 255, 255];   // 純白
        
        if (isset($players['white'])) {
            // 上半部使用漸變銀色
            $gradientHeight = $h;
            $steps = 15;
            
            // 先繪製漸變背景
            for ($i = 0; $i < $steps; $i++) {
                $stepY = $y + $h + ($gradientHeight / $steps) * $i;
                $stepHeight = $gradientHeight / $steps;
                
                // 從白漸變到銀
                $ratio = $i / $steps;
                $rColor = $white[0] - ($white[0] - $silver[0]) * $ratio;
                $gColor = $white[1] - ($white[1] - $silver[1]) * $ratio;
                $bColor = $white[2] - ($white[2] - $silver[2]) * $ratio;
                
                if (get_class($this->pdf) == 'Mpdf\Mpdf') {
                    $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                    $this->pdf->Rect($x, $stepY, $w, $stepHeight, 'F');
                } else {
                    $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                    $this->pdf->Rect($x, $stepY, $w, $stepHeight, 'F');
                }
            }
            
            // 繪製上半部圓角邊框（只繪製邊框，不填充）
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
            
            // 下半部保持原本的藍色
            $this->pdf->RoundedRect($x, $y + $h, $w, $h, $r, '0110', 'D', $this->styleBoxLine, $this->boxBlueColor);
            // dd($players['white']);
            $this->pdf->setXY($x, $y - $this->athleteGap);
            if($players['white']['is_weight_passed'] == 0){
                $this->pdf->setFont($this->generalFont, 'D', $this->circleFontSize);
            }
            $this->pdf->Cell($this->boxW, $h, $this->smartTruncate($players['white']['name']) . ' ' . $this->smartTruncate($players['white']['name_secondary']) , 0, 1, 'L', 0, '', 0);
            $this->pdf->setFont($this->generalFont, '', $this->circleFontSize);
            $this->pdf->setXY($x, $y + $this->athleteGap);
            $this->pdf->Cell($this->boxW, $h, $players['white']['team'] , 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x, $y + $h - $this->athleteGap);
            if($players['blue']['is_weight_passed'] == 0){
                $this->pdf->setFont($this->generalFont, 'D', $this->circleFontSize);
            }
            $this->pdf->Cell($this->boxW, $h, $this->smartTruncate($players['blue']['name']) . ' ' . $this->smartTruncate($players['blue']['name_secondary']), 0, 1, 'L', 0, '', 0);
            $this->pdf->setFont($this->generalFont, '', $this->circleFontSize); 
            $this->pdf->setXY($x, $y + $h + $this->athleteGap);
            $this->pdf->Cell($this->boxW, $h, $players['blue']['team'] , 0, 1, 'L', 0, '', 0);
        } else {
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->styleBoxLine, $this->boxBlueColor);
            $this->pdf->setXY($x, $y);
            $this->pdf->Cell($this->boxW, $h, $this->smartTruncate($players['blue']['name']) . $this->smartTruncate($players['blue']['name_secondary']), 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x, $y + $this->athleteGap);
            $this->pdf->Cell($this->boxW, $h, $players['blue']['team'] , 0, 1, 'L', 0, '', 0);
        }
    }
    
    private function arcLine($x, $y, $arcW, $h, $num = 0, $winner = 0, $first = false, $players = null)
    {
        $size = $this->circleSize;
        // $style = array('L' => 0,
        //                 'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
        //                 'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
        //                 'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor));
        // $this->pdf->Rect($x, $y, $arcW, $h, 'D', $style );
        $x += $arcW;
        $y += $h / 2;
        $styleWinnerLine = $this->winnerLineDraw ? $this->styleWinnerLine : $this->styleArcLine;
        $styleArcLine = $this->styleArcLine;
        if ($players && isset($players['blue'])) {
            $this->pdf->line($x, $y + $h / 2, $x - $this->arcW, $y + $h / 2, $styleWinnerLine);
        }
        /* winner line */
        if ($winner == 1) {
            if ($first) {
                $this->pdf->line($x - $arcW, $y - $h / 2, $x, $y - $h / 2, $styleWinnerLine);
                $this->pdf->line($x - $arcW, $y + $h / 2, $x, $y + $h / 2, $styleArcLine);
            }
            $this->pdf->line($x, $y - ($h / 2), $x, $y, $styleWinnerLine);
            //$this->pdf->line($x, $y, $x+$this->arcW, $y, $styleWinnerLine);
            if ($x > 180) {
                $this->pdf->line($x, $y, $x - $this->arcW, $y, $styleWinnerLine);
            } else {
                $this->pdf->line($x, $y, $x + $this->arcW, $y, $styleWinnerLine); //$styleWinnerLine of previours
            }

            $this->pdf->line($x, $y + $h / 2, $x, $y, $styleArcLine);
        } else if ($winner == 2) {
            if ($first) {
                $this->pdf->line($x - $arcW, $y - $h / 2, $x, $y - $h / 2, $styleArcLine);
                $this->pdf->line($x - $arcW, $y + $h / 2, $x, $y + $h / 2, $styleWinnerLine);
            }
            $this->pdf->line($x, $y + $h / 2, $x, $y, $styleWinnerLine);
            //$this->pdf->line($x, $y, $x+$this->arcW, $y, $styleWinnerLine);
            if ($x > 180) {
                $this->pdf->line($x, $y, $x - $this->arcW, $y, $styleWinnerLine);
            } else {
                $this->pdf->line($x, $y, $x + $this->arcW, $y, $styleWinnerLine);
            }

            $this->pdf->line($x, $y, $x, $y - $h / 2, $styleArcLine);
        } else {
            if ($first) {
                $this->pdf->line($x - $arcW, $y - $h / 2, $x, $y - $h / 2, $styleArcLine);
                $this->pdf->line($x - $arcW, $y + $h / 2, $x, $y + $h / 2, $styleArcLine);
            }
            $this->pdf->line($x, $y - $h / 2, $x, $y + $h / 2, $styleArcLine);
            $this->pdf->line($x, $y, $x + $this->arcW, $y, $styleArcLine);
        }

        /* circle sequence number */
        if (!$this->winnerLineDraw) {
            $this->pdf->Ellipse($x, $y, $size, 0, 360, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
            $this->pdf->setXY($x - $size, $y - $size);
            $this->pdf->setFont($this->generalFont, '', $this->circleFontSize);
            $this->pdf->Cell($size * 2, $size * 2, $num, 0, 1, 'C', 0, '', 0);
        }
    }

    private function resultBox($winnerList)
    {
        $x = $this->resultXY[0];
        $y = $this->resultXY[1];
        $w = 60;
        $h = 35;
        $r = 3.50;
        
        // 繪製結果框
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->styleResult2, $this->resultColor2);
        
        $x1 = $x;
        $y1 = $y;
        $h1 = 7;
        
        for ($i = 0; $i < count($winnerList); $i++) {
            // 排名部分
            $this->pdf->setXY($x1, $y1 + 6);
            $this->pdf->SetFont($this->generalFont, 'B', 10);
            $this->pdf->Cell(0, 0, $winnerList[$i]['award'] . ':', 0, 1, 'L', 0, '', 0);
            
            // 運動員名字部分（置左對齊）
            $this->pdf->setXY($x1 + 5, $y1 + 5);
            $this->pdf->SetFont($this->playerFont, 'B', 10);
            $this->pdf->Cell($w - 15, 0, $winnerList[$i]['name'], 0, 1, 'L', 0, '', 0);
            
            $y1 += $h1;
        }

        // 標題框
        $x = $x + 5;
        $y = $y - 5;
        $w = $w - 10;
        $h = 10;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->styleResult1, $this->resultColor1);
        $this->pdf->setXY($x, $y);
        $this->pdf->SetFont($this->generalFont, 'B', 14);
        $this->pdf->Cell($w, 10, '比賽結果', 0, 1, 'C', 0, '', 0);
    }

    private function smartTruncate($name, $maxLength = 21)
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
