<?php
namespace App\Services\Printer;
use App\Helpers\PdfHelper;

use TCPDF;

class RoundRobbinOption1Service{
    
    protected $gameSetting=array(
        '2'=>array(
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
        '3'=>array(
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
        '4'=>array(
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
        '5'=>array(
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
        '6'=>array(
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
    protected $logo_primary='';
    protected $logo_secondary=null;

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
        5=>[[0,2],[0,4],[2,4],[2,4]]
    ];
    protected $colors=[
        'first'=>[
            '254,206,50',
            '245,158,51'
        ],
        'second'=>[
            '93,177,61',
            '147,193,58'
        ]
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
    public function setTitles($title=null, $title_sub=null){
        $this->title=$title;
        $this->title_sub=$title_sub;
    }

    public function pdf($players=[],$winners=[], $sequences=[], $winnerList=[], $ellipseData = []){
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
        // dd($players);
         if(count($players)==5){
            // dd($players);
            $this->gameTable5($players);
            // dd($players);
            $this->boxPlayers5($players);
        }else if(count($players)==4){
            $this->boxPlayers4($players);
        }
        $this->resultBox($winnerList);
        $this->pdf->Output('myfile.pdf', 'I');
    }

    public function gameTable5($players){
        
        $p1=$players;
        $x=$this->startX;
        $y=$this->startY;
        $this->drawTable5($p1, $x, $y,$this->colors['first']);
        // $p2=$players;
        // $x+=50;
        // $y+=30;
        // $this->drawTable5($p2, $x, $y,$this->colors['second']);
    }
    private function drawTable5($players, $x, $y, $colors){

        $this->pdf->setXY($x, $y);
        //$this->pdf->setXY($this->startX, $this->startY);
        $cnt=count($players);
        $tbl ='
        <table cellspacing="0" cellpadding="1" border="0">
            <tr><th class="narrow"></th><th class="player"></th>';
        foreach($players as $i=>$p){
            $tbl.='<th class="num0">'.($i+1).'</th>';

        }
        $tbl.='<th class="num1"></th><th class="num1"></th></tr>';
        foreach($players as $i=>$p1){
            $tbl.='<tr><td class="num0">'.($i+1).'</td><td>'.$players[$i]['name_display'].'</td>';
            foreach($players as $j=>$p2){
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
            .num0{
                background-color:rgb('.$colors[0].');
                width:22px;
                text-align:center;
            }
            .num1{
                background-color:rgb('.$colors[1].');
                width:20px;
                text-align:center;
            }
        </style>
        ';
        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }

    private function boxPlayers5($players){
        $style = array('L' => 0,
                'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor));

        $x=$this->startX;
        $y=$this->startY+60;
        $h=$this->boxH/2;
        $w=$this->boxW;
        $gap=2;
        $r=2;
        $lineW=30;
        $cnt=count($players);
        $sequences=[1,3,4,2];
        $game=[[0,2],[0,4],[2,4],[1,3]];
        $color=explode(',',$this->colors['first'][0]);
        foreach($game as $i=>$g){
            if($i==3){
                $x+=$this->boxW/3;
                $color=explode(',',$this->colors['second'][0]);
            }

            $this->pdf->setFont($this->playerFont,'',$this->playerFontSize);
            $this->pdf->setXY($x, $y);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
            $this->pdf->RoundedRect($x, $y+$h, $w, $h, $r, '0110', 'DF', $this->styleBoxLine, $this->boxBlueColor);
            $this->pdf->setXY($x, $y);
            // dd($players);
            $this->pdf->Cell($this->boxW, $h, $players[$g[0]]['name_display'], 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x, $y+$h);
            $this->pdf->Cell($this->boxW, $h, $players[$g[1]]['name_display'], 0, 1, 'L', 0, '', 0);
            $this->pdf->RoundedRect($x+$this->boxW, $y+($h/2), $gap, $h, $r, '0000', 'DF', $style, $color);
            $this->pdf->circle($x+$this->boxW+$gap+2, $y+$h, 2, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
            $this->pdf->setFont('times','',10);

            $this->pdf->text($x+$this->boxW+$gap, $y+$h-2, $sequences[$i]);
            $y+=$this->boxH+$this->boxGap;
        }
        //first horizontal line
        $x=$this->startX+$this->boxW+$gap+4;
        $y=$this->startY+60+($this->boxH/2);
        $x2=$x+$this->arcW;
        $this->pdf->line($x, $y, $x2 ,$y);
        // first virtical line
        $y2=$y+($this->boxH+$this->boxGap)*2;
        $this->pdf->line($x2, $y, $x2 ,$y2);
        $h=($this->boxH+$this->boxGap)*2;
        $this->pdf->rect($x2-$gap, $y, $gap ,$h,'DF', $style, explode(',',$this->colors['first'][0]));
        // middle long lineline
        $y+=$this->boxH+$this->boxGap;
        $this->pdf->line($x, $y, $x+$this->arcW*2,$y);

        // second vertical line
        $x1=$x2+$this->arcW;
        $y1=$y;
        $y2=$y1+($this->boxH+$this->boxGap)*2;
        $this->pdf->line($x1, $y1, $x1,$y2);
        $h=($y2-$y1)/2;
        $this->pdf->rect($x1-$gap, $y, $gap ,$h,'DF', $style, explode(',',$this->colors['first'][0]));
        $y1=$y+$h;
        $this->pdf->rect($x1-$gap, $y1, $gap ,$h,'DF', $style, explode(',',$this->colors['second'][0]));
        $this->pdf->circle($x1, $y1, 2, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
        $this->pdf->setFont('times','',10);
        $this->pdf->text($x1-$gap, $y1-2.5, '6');

        // third horizontal line
        $y+=$this->boxH+$this->boxGap;
        $this->pdf->line($x, $y, $x+$this->arcW,$y);

        $y+=$this->boxH+$this->boxGap;
        $x+=$this->boxW/3;
        $x2=$x+$this->arcW+$gap+3;
        $this->pdf->line($x, $y, $x2,$y);
        
        /* to repechage section */
        $x=$x;
        $y=$y+($this->boxH*1);
        $x2=$x+$this->arcW;
        $this->pdf->line($x, $y, $x2,$y);
        $y2=$y+$this->boxH*0.5;
        $this->pdf->line($x, $y2, $x2,$y2);
        $x=$x2;
        $this->pdf->line($x, $y, $x2,$y2);
        $h=($y2-$y)/2;
        $this->pdf->rect($x, $y, $gap ,$h,'DF', $style, explode(',',$this->colors['first'][0]));
        $this->pdf->rect($x, $y+$h, $gap ,$h,'DF', $style, explode(',',$this->colors['second'][0]));
        $this->pdf->line($x, $y+$h, $x+$this->arcW,$y+$h);
        $this->pdf->circle($x+$gap, $y+$h, 2, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
        $this->pdf->text($x, $y+($h/2), '5');
    }
    private function boxPlayers4($players){

        $style = array('L' => 0,
                'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor));

        $x=$this->startX;
        $y=$this->startY;
        $h=$this->boxH/2;
        $w=$this->boxW;
        $gap=2;
        $r=2;
        $lineW=30;
        $cnt=count($players);
        $sequences=[1,2,3];
        $game=[[0,2],[1,3],[0,0]];
        $color=explode(',',$this->colors['first'][0]);
        foreach($game as $i=>$g){
            $playerWhite=$players[$g[0]]['name_display'];
            $playerBlue=$players[$g[1]]['name_display'];
            if($i==2){
                $x+=$this->boxW/3;
                $color=explode(',',$this->colors['second'][0]);
                }

            $this->pdf->setFont($this->playerFont,'',$this->playerFontSize);
            $this->pdf->setXY($x, $y);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
            $this->pdf->RoundedRect($x, $y+$h, $w, $h, $r, '0110', 'DF', $this->styleBoxLine, $this->boxBlueColor);
            $this->pdf->setXY($x, $y);
            $this->pdf->Cell($this->boxW, $h, $playerWhite, 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x, $y+$h);
            $this->pdf->Cell($this->boxW, $h, $playerBlue, 0, 1, 'L', 0, '', 0);
            $this->pdf->RoundedRect($x+$this->boxW, $y+($h/2), $gap, $h, $r, '0000', 'DF', $style, $color);
            $this->pdf->circle($x+$this->boxW+$gap+2, $y+$h, 2, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
            $this->pdf->setFont('times','',10);

            $this->pdf->text($x+$this->boxW+$gap, $y+$h-2, $sequences[$i]);
            $y+=$this->boxH+$this->boxGap;
        }
        $x=$this->startX+$this->boxW+$gap+4;
        $y1=$this->startY+($this->boxH/2);
        $this->pdf->line($x, $y1, $x+$this->arcW, $y1);

        $y2=$y1+$this->boxH+$this->boxGap;
        $this->pdf->line($x, $y2, $x+$this->arcW, $y2);
        $x=$x+$this->arcW;
        $this->pdf->line($x, $y1, $x, $y2);
        $y1+=($y2-$y1)/2;
        $this->pdf->line($x, $y1, $x+$this->arcW, $y1);
        $y1+=$this->boxH+$this->boxGap+$this->boxH;
        $this->pdf->line($x, $y1, $x+$this->arcW, $y1);
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

    public function setFonts($titleFont = 'times', $playerFont = 'times', $generalFont = 'times')
    {
        $this->titleFont = $titleFont;
        $this->playerFont = $playerFont;
        $this->generalFont = $generalFont;
    }
}