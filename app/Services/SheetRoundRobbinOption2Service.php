<?php
namespace App\Services;

use TCPDF;

class SheetRoundRobbinOption2Service{
    
    protected $gameSetting=array(
        '4'=>array(
            'startX'=>25,
            'startY'=>35,
            'boxW'=>60, //運動員名牌高度
            'boxH'=>20, //運動員名牌寛度
            'boxGap'=>6, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>15,
            'repechageBoxGap'=>6,
            'repechageSectionGap'=>10,
            'circleSize'=>3,
            'playerFontSize'=>14
        ),
        '8'=>array(
            'startX'=>25,
            'startY'=>35,
            'boxW'=>60, //運動員名牌高度
            'boxH'=>20, //運動員名牌寛度
            'boxGap'=>6, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>15,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
            'playerFontSize'=>14
        ),
        '16'=>array(
            'startX'=>25,
            'startY'=>35,
            'boxW'=>60, //運動員名牌高度
            'boxH'=>15, //運動員名牌寛度
            'boxGap'=>6, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>10,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
            'playerFontSize'=>12
        ),
        '32'=>array(
            'startX'=>25,
            'startY'=>35,
            'boxW'=>60, //運動員名牌高度
            'boxH'=>10, //運動員名牌寛度
            'boxGap'=>2, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>5,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
            'playerFontSize'=>10
        ),
        '64'=>array(
            'startX'=>25,
            'startY'=>23,
            'boxW'=>55, //運動員名牌高度
            'boxH'=>6.2, //運動員名牌寛度
            'boxGap'=>1, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>1,
            'repechageBoxGap'=>1,
            'repechageSectionGap'=>1,
            'circleSize'=>2,
            'playerFontSize'=>8
        ),
    );
    protected $pdf=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';

    protected $startX=25; //面頁基點X軸
    protected $startY=30; //面頁基點Y軸
    protected $boxW=45; //運動員名牌高度
    protected $boxH=14; //運動員名牌寛度
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
    protected $boxWhiteColor=array(240,240,255);
    protected $boxBlueColor=array(255,255,255);
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
        4=>[[0,1],[2,3],[0,2],[1,3],[0,0],[1,2]],
        5=>[[0,1],[2,3],[0,4],[1,2],[3,4],[0,2],[1,3],[2,4],[0,3],[1,4]]
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

    public function setTitles($title=null, $title_sub=null){
        $this->title=$title;
        $this->title_sub=$title_sub;
    }
    public function setFonts($titleFont='times', $playerFont='times',$generalFont='times'){
        $this->titleFont=$titleFont;
        $this->playerFont=$playerFont;
        $this->generalFont=$generalFont;
    }

    public function pdf($players=[], $winners=[], $sequences=[], $winnerList=[]){
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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

        $this->pdf->Cell(0, 0, $this->title, 0, 1, 'C', 0, '', 0);
        $this->pdf->Cell(0, 0, $this->title_sub, 0, 1, 'C', 0, '', 0);
        
        
        $this->gameTable($players);
        $this->boxPlayers($players);
        $this->resultBox($winnerList);

        $this->pdf->Output('myfile.pdf', 'I');
    }
    public function gameTable($players){
        $this->pdf->setXY($this->startX, $this->startY);
        $cnt=count($players);
        $tbl ='
        <table cellspacing="0" cellpadding="1" border="0">
            <tr><th class="narrow"></th><th class="player"></th>';
        for($i=0; $i<$cnt; $i++){
            $tbl.='<th class="num">'.($i+1).'</th>';

        }
        $tbl.='<th class="num2"></th><th class="num2"></th></tr>';
        for($i=0;$i<$cnt;$i++){
            $tbl.='<tr><td class="num">'.($i+1).'</td><td>'.$players[$i]['name_display'].'</td>';
            for($j=0;$j<$cnt;$j++){
                if($i==$j){
                    $tbl.='<td class="block"></td>';
                }else{
                    $tbl.='<td></td>';
                }
            }
            $tbl.='<td></td><td></td></tr>';
        }
        $tbl.='
        </table>
        <style>
            table tr td, table tr th{
                border: 1 solid darkgray;
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
                width:200px;
            }
            .num{
                background-color:rgb(254,206,50);
                width:22px;
                text-align:center;
            }
            .num2{
                background-color:rgb(245,158,51);
                width:20px;
                text-align:center;
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
        $y=$this->startY+50;
        $h=$this->boxH/2;
        $w=$this->boxW;
        $gap=2;
        $r=2;
        $lineW=30;
        $cnt=count($players);
        $game=$this->gameRound[$cnt];
        foreach($game as $g){
            $this->pdf->setXY($x, $y);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
            $this->pdf->RoundedRect($x, $y+$h, $w, $h, $r, '0110', 'DF', $this->styleBoxLine, $this->boxBlueColor);
            $this->pdf->setXY($x, $y);
            $this->pdf->Cell($this->boxW, $h, $players[$g[0]]['name_display'], 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x, $y+$h);
            $this->pdf->Cell($this->boxW, $h, $players[$g[1]]['name_display'], 0, 1, 'L', 0, '', 0);
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
    private function resultBox($winnerList){
        $x=$this->resultXY[0];
        $y=$this->resultXY[1];
        //$y=$this->pdf->getPageHeight()/2;
        $w=45;
        $h=30;
        $r=3.50;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->styleResult2, $this->resultColor2);
        $x1=$x;
        $y1=$y;
        $w1=$w;
        $h1=6;
        for($i=0;$i<count($winnerList);$i++){
            $this->pdf->setXY($x1, $y1+5);
            $this->pdf->SetFont($this->generalFont, 'B', 10);
            $this->pdf->Cell(0, 0, $winnerList[$i]['award'].':', 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x1+15, $y1+5);
            $this->pdf->SetFont($this->playerFont, 'B', 10);
            $this->pdf->Cell($w-15, 0, $winnerList[$i]['name'].':', 0, 1, 'L', 0, '', 0);
            $y1+=$h1;
        }

        //$this->pdf->Cell($w, $h-5, $winnerList[0]['award'], 1, 1, 'L', 0, '', 0);

        $x=$x+5;
        $y=$y-5;
        $w=$w-10;
        $h=10;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->styleResult1, $this->resultColor1);
        $this->pdf->setXY($x, $y);
        $this->pdf->SetFont($this->generalFont, 'B', 14);
        $this->pdf->Cell(35, 10, 'Results', 0, 1, 'C', 0, '', 0);

    }

}