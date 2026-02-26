<?php
namespace App\Services\Printer;
use App\Helpers\PdfHelper;
use App\Services\CustomTCPDF;
use TCPDF;

class RoundRobbinOption2Service{
    
    protected $gameSetting=null;
    protected $pdf=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';
    protected $logo_primary = 'images/mja_logo.png';
    protected $logo_secondary=null;

    protected $startX=25; //面頁基點X軸
    protected $startY=30; //面頁基點Y軸
    protected $boxW=45; //運動員名牌高度
    protected $boxH=18; //運動員名牌寛度
    protected $boxGap=4; //運動員名牌之間距離
    protected $arcW=20; //上線曲線寛度
    protected $arcWFirst=4; //第一輪上線曲線
    protected $repechageBoxH=5;
    protected $repechageDistance=5; //復活賽表,運動員名牌之間距離
    protected $repechageBoxGap=2; //復活賽表,運動員名牌之間距離
    protected $repechageSectionGap=5; //復活賽表, second
    protected $circleSize=3;
    protected $playerFontSize=10;

    protected $repechageMode='QUARTER'; //QUARTER, DOUBLE, FULL 
    protected $round=0;
    protected $playerCount=0;
    
    protected $titleFont='times';
    protected $playerFont='times';
    protected $generalFont='times';
    
    protected $arcColor=array(50, 50, 127);
    protected $boxWhiteColor=array(255,255,255);
    protected $boxBlueColor=array(240,240,255);
    protected $circleColor=array(240,240,240);
    protected $styleWinnerLine=null;
    protected $styleArcLine=null;
    protected $styleBoxLine=null;
    protected $styleResult1=null;
    protected $styleResult2=null;
    protected $styleCircle=null;
    protected $resultColor1=array(200,200,200);
    protected $resultColor2=array(250,250,250);
    protected $resultXY=[120,120];

    protected $gameRound=[
        3=>[[0,1],[0,2],[1,2]],
        4=>[[0,1],[2,3],[0,2],[1,3],[0,3],[1,2]],
        5=>[[1,4],[2,3],[0,4],[1,2],[0,1],[3,4],[0,2],[1,3],[0,3],[2,4]]
    ];

    public function __construct($settings){
        $this->gameSetting=$settings;
        $this->styleWinnerLine = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $this->styleArcLine = array('width' => 0.10, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50, 127));
        $this->styleBoxLine= array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $this->styleResult1 = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleResult2 = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleCircle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50,127));

    }
    public function setLogos($primary=null, $secondary=null){
        $this->logo_primary=$primary;
        $this->logo_secondary=$secondary;
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

    public function pdf($players = [], $winners = [],  $sequences = [], $winnerList = [], $ellipseData = [], $repechagePlayers = [], $repechage = true){
        $this->pdf = new CustomTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->playerCount=count($players);
        foreach($this->gameSetting[$this->playerCount] as $key=>$value){
            $this->$key=$value;
        }
        // set margins
        //$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //$this->pdf->SetFooterMargin(0);
        $this->pdf->SetPrintHeader(false);
        // $this->pdf->SetPrintFooter(false);
        $this->pdf->SetMargins(15,10,15);
        $this->pdf->SetAutoPageBreak(TRUE,0);
        $this->pdf->AddPage();
        $helper=new PdfHelper($this->pdf);
        $helper->header1(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont, $ellipseData);
        $this->gameTable($players);
        $this->boxPlayers($players);
        $this->resultBox($winnerList);
        if($this->playerCount == 4 || $this->playerCount == 5){
            $this->pdf->setXY(200, 0);
            $this->pdf->Cell(12, 0, '改' , 0, 1, 'L', 0, '', 0);
        }
        $this->pdf->Output('myfile.pdf', 'I');
    }

    public function multiPdf($pdf,$players = [], $winners = [],  $sequences = [], $winnerList = [], $ellipseData = [], $repechagePlayers = [], $repechage = true){
        $this->pdf = $pdf;
        $this->playerCount=count($players);
        foreach($this->gameSetting[$this->playerCount] as $key=>$value){
            $this->$key=$value;
        }
        // set margins
        //$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //$this->pdf->SetFooterMargin(0);
        $this->pdf->SetPrintHeader(false);
        // $this->pdf->SetPrintFooter(false);
        $this->pdf->SetMargins(15,10,15);
        $this->pdf->SetAutoPageBreak(TRUE,0);
        $this->pdf->AddPage();
        $helper=new PdfHelper($this->pdf);
        $helper->header1(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont, $ellipseData);
        $this->gameTable($players);
        $this->boxPlayers($players);
        $this->resultBox($winnerList);
        if($this->playerCount == 4 || $this->playerCount == 5){
            $this->pdf->setXY(200, 0);
            $this->pdf->Cell(12, 0, '改' , 0, 1, 'L', 0, '', 0);
        }

        return $this->pdf ;
    }

    public function gameTable($players){
        $this->pdf->setXY($this->startX -3 , $this->startY);
        $cnt=count($players);
        $tbl ='
        <table cellspacing="0" cellpadding="1" border="0">
            <tr><th class="narrow"></th><th class="player"></th>';
        for($i=0; $i<$cnt; $i++){
            $tbl.='<th class="num">'.($i+1).'</th>';

        }
        $tbl.='<th class="num2">WON</th>
        <th class="num2">SCORE</th>
        <th class="num2">RANK</th></tr>';

        for($i=0;$i<$cnt;$i++){
            $tbl.='<tr><td class="num1">'.($i+1).'</td><td class="playerbox">'.
                '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.
                '<tr>'.
                '<td width="50%" style="vertical-align:top;border:none;">'.
                $this->smartTruncate($players[$i]['name']).'<div>'. $this->smartTruncate($players[$i]['name_secondary']) .'</div>'.
                '</td>'.
                '<td width="50%" style="vertical-align:center;border:none;text-align:right; font-size:9px;">'.
                '<div>'. $this->smartTruncate($players[$i]['team']['abbreviation']) .'</div>'.
                ($players[$i]['team']['name'] ).
                '</td>'.
                '</tr>'.
                '</table>'.
                '</td>';
            for($j=0;$j<$cnt;$j++){
                if($i==$j){
                    $tbl.='<td class="block"></td>';
                }else{
                    $tbl.='<td></td>';
                }
            }
            $tbl.='<td></td><td></td><td></td></tr>';
        }
        $tbl.='
        </table>
        <style>
            table tr td, table tr th{
                border: 1 solid black;
                height: 18px;
            }
            .block{
                background-color:lightgray;
            }
            .narrow{
                border: none;
                width:22px
            }
            .player{
                border: none;
                width:220px;
            }
            .num{
                background-color:rgb(254,206,50);
                width:22px;
                line-height:18px;
                text-align:center;
            }
            .num1{
                background-color:rgb(254,206,50);
                width:22px;
                line-height:22px;
                text-align:center;
            }
            .num2{
                background-color:rgb(245,158,51);
                width:50px;
                line-height:18px;
                text-align:center;
            }
            .playerbox{
                font-size:10px;
                width:220px;
            }
        </style>
        ';
        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }

    private function boxPlayers($players){
        $style = array('L' => 0,
                'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor));

        $x=$this->startX;
        $y=$this->startY+65;
        $h=$this->boxH/2;
        $w=$this->boxW;
        $gap=2;
        $r=2;
        $lineW=30;
        $cnt=count($players);
        $game=$this->gameRound[$cnt];

        $this->pdf->SetFontSize(8);

        foreach($game as $g){
            $this->pdf->setXY($x, $y);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
            $this->pdf->RoundedRect($x, $y+$h, $w, $h, $r, '0110', 'DF', $this->styleBoxLine, $this->boxBlueColor);
            $this->pdf->setXY($x + 1, $y);
            $this->pdf->Cell($this->boxW , $h - 4, $players[$g[0]]['name'] . $players[$g[0]]['name_secondary'], 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x + 1, $y + ($h/5));
            $this->pdf->Cell($this->boxW, $h,($players[$g[0]]['team']['abbreviation'] ? $players[$g[0]]['team']['abbreviation'] . '-' : '') . $players[$g[0]]['team']['name'], 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x + 1, $y + $h - 2);
            $this->pdf->Cell($this->boxW, $h, $players[$g[1]]['name'] . $players[$g[1]]['name_secondary'], 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x + 1, $y + $h + ($h/5));
            $this->pdf->Cell($this->boxW, $h,($players[$g[1]]['team']['abbreviation'] ? $players[$g[1]]['team']['abbreviation'] . '-' : '') . $players[$g[1]]['team']['name'] , 0, 1, 'L', 0, '', 0);
            $this->pdf->RoundedRect($x+$this->boxW, $y+($h/2), $gap, $h, $r, '0000', 'DF', $style, array(254,206,50));
            $this->pdf->circle($x+$this->boxW+$gap+2, $y+($h/2)+($h/2), 2, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
            $x1=$x+$this->boxW+$gap;
            $y1=$y+($this->boxH/2);
            $x2=$x1+$lineW;
            $y2=$y1;
            $this->pdf->line($x1+($gap*2), $y1, $x2, $y2, $this->styleBoxLine);
            $y+=$this->boxH+$this->boxGap;
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
    private function smartTruncate($name, $maxLength = 15)
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