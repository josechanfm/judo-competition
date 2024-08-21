<?php
namespace App\Services;

use TCPDF;

class TournamentChartService{

    protected $gameSize=array(
        '4'=>array(
            'boxW'=>45, //運動員名牌高度
            'boxH'=>20, //運動員名牌寛度
            'boxGap'=>6, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>5,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
        ),
        '8'=>array(
            'boxW'=>45, //運動員名牌高度
            'boxH'=>20, //運動員名牌寛度
            'boxGap'=>6, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>5,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
        ),
        '16'=>array(
            'boxW'=>45, //運動員名牌高度
            'boxH'=>15, //運動員名牌寛度
            'boxGap'=>6, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>5,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
        ),
        '32'=>array(
            'boxW'=>45, //運動員名牌高度
            'boxH'=>10, //運動員名牌寛度
            'boxGap'=>2, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>5,
            'repechageBoxGap'=>2,
            'repechageSectionGap'=>5,
            'circleSize'=>3,
        ),
        '64'=>array(
            'boxW'=>45, //運動員名牌高度
            'boxH'=>6.2, //運動員名牌寛度
            'boxGap'=>0.5, //運動員名牌之間距離
            'arcW'=>20, //上線曲線寛度
            'arcWFirst'=>4, //第一輪上線曲線
            'repechageDistance'=>5,
            'repechageBoxGap'=>0.5,
            'repechageSectionGap'=>2,
            'circleSize'=>3,
        ),
    );
    protected $pdf=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';
    protected $startX=25; //面頁基點X軸
    protected $startY=30; //面頁基點Y軸

    protected $boxW=45; //運動員名牌高度
    protected $boxH=10; //運動員名牌寛度
    protected $boxGap=2; //運動員名牌之間距離
    protected $arcW=20; //上線曲線寛度
    protected $arcWFirst=4; //第一輪上線曲線
    protected $repechageDistance=5; //復活賽表,運動員名牌之間距離
    protected $repechageBoxGap=2; //復活賽表,運動員名牌之間距離
    protected $repechageSectionGap=5; //復活賽表, second
    protected $circleSize=3;

    protected $repechage='QUARTER'; //KNOCKOUT, FULL, QUARTER, DOUBLE, TWOTHIRD, CONSOLATION
    protected $round=0;
    protected $playerCount=0;
    

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
    protected $poolLable=[
        ['name'=>'Pool A','color'=>array(243,151,0)],
        ['name'=>'Pool B','color'=>array(230,39,37)],
        ['name'=>'Pool C','color'=>array(122,184,42)],
        ['name'=>'Pool D','color'=>array(98,197,230)]
    ];
    protected $resultColor1=array(200,200,200);
    protected $resultColor2=array(250,250,250);

    public function __construct()
    {
        $this->styleWinnerLine = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $this->styleArcLine = array('width' => 0.10, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50, 127));
        $this->styleBoxLine= array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $this->styleResult1 = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleResult2 = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleCircle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50,127));
    }
    public function pdf($players=[], $winners=[], $repechagePlayers=[], $repechageWinners=[], $sequences=[], $winnerList=[],$poolLabel=null){
        if($poolLabel){
            $this->poolLable=$poolLabel;
        };
        $this->pdf = new TCPDF();
        $this->pdf->AddPage();

        $this->playerCount=count($players);
        foreach($this->gameSize[$this->playerCount*2] as $key=>$value){
            $this->$key=$value;
        }

        $this->round=strlen((string)decbin($this->playerCount));
        $this->pdf->SetFont('times', 'B', 14);
        $this->pdf->Cell(0, 0, $this->title, 0, 1, 'C', 0, '', 0);
        $this->pdf->Cell(0, 0, $this->title_sub, 0, 1, 'C', 0, '', 0);
        $this->pdf->SetFont('times', '', 10);

        $this->mainChart($players); //主上線表包括運動員名牌和上線曲線
        $this->repechageChart(count($players),$repechagePlayers); //復活賽上線表包括運動員名牌和上線曲線
        if($this->playerCount>4){
            $this->poolBox($this->poolLable);
        }
        $this->winnerLine($winners);
        $this->repechageWinnerLine(count($players),$repechageWinners);
        $this->sequenceNumbers($sequences);
        $this->resultBox($winnerList);
        $this->pdf->Output('myfile.pdf', 'I');
    }
    private function poolBox($poolLable){
        $x=$this->startX-10;
        $y=$this->startY+($this->boxH/4)+$this->boxGap;
        //$h=($this->boxH+$this->boxGap)*($this->playerCount/4)-($this->boxGap/2)-($this->boxH/2)-$this->boxH;
        $h=($this->boxH+$this->boxGap)*($this->playerCount/4)-($this->boxGap/2)-($this->boxH);
        for($i=3; $i>=0; $i--){
            $this->pdf->RoundedRect($x, $y, 6.5, $h, 3.25, '1111', 'DF',$this->styleCircle, $poolLable[$i]['color']);
            $y+=$h+($this->boxH*1.5)+($this->boxGap/2);
        }

        //$x=$this->startX-10;
        $y=$this->startY+(($this->boxH+$this->boxGap)*$this->playerCount)-($this->boxGap /2);
        $w=(($this->boxH+$this->boxGap)*$this->playerCount/4);
        $this->pdf->setFont('times','B', 14);
        $this->pdf->StartTransform();
        $this->pdf->setXY($x, $y);
        $this->pdf->Rotate(90);
        for($i=3; $i>=0; $i--){
            $this->pdf->Cell($w,0,$poolLable[$i]['name'],1,0,'C',0,'');
        }
        $this->pdf->StopTransform();
    }
    private function mainChart($players){
        /* 運動員名牌 */
        $boxX=$this->startX; //box X axis, do prevent the change of startX original value,
        $boxY=$this->startY; //box Y axis, do prevent the change of the startY original value,
        $arcWFirst=$this->arcWFirst;
        /* player box */
        for($i=0;$i<$this->playerCount;$i++){
            $this->boxPlayers($this->pdf, $boxX, $boxY, $this->boxW, $this->boxH, $players[$i]);
            $boxY+=$this->boxGap+$this->boxH; //accumulated for the text box starting point
        }

        /* 第一輪上線曲線 */
        $px=$this->startX+$this->boxW; //starting point of $x axis
        $py=$this->startY+($this->boxH/4); //starting point of $y axis
        $pW=$this->arcWFirst; //width of arch shape
        $h=$this->boxH / 2; //height of arch shape
        $pGap= $h*2+$this->boxGap; //$h +$boxH+$boxGap; //gap betwee each arch in vertical alignment
        for($i=0;$i<$this->playerCount;$i++){
            $this->arcLine($this->pdf,$px, $py, $pW, $h);
            $py+=$pGap;
        }

        /* 第一輪以外的上線曲線  */        
        $ax=$this->startX+$this->boxW+$arcWFirst;
        $ah=$this->boxH+$this->boxGap;
        $ay=$this->startY+($this->boxH/2);
        $cnt=$this->playerCount/2;
        for($i=1;$i<=$this->round-1;$i++){
            $arcGap=$ah *2; //gap betwee each arch in vertical alignment
            for($j=0;$j<$cnt;$j++){
                $this->arcLine($this->pdf,$ax, $ay, $this->arcW, $ah);
                $ay+=$arcGap;
            }
            $ax+=$this->arcW;
            //$ay=$this->startY+$ah-$this->boxGap+($this->boxH/4)+($this->arcWFirst/2);
            $ay=$this->startY+$ah-($this->boxGap/2);
            $ah+=$ah;
            $cnt/=2;
        }
        $this->pdf->line($ax, $ay, $ax+$this->arcW, $ay);
    }
    private function winnerLine($winners){
        $arcWFirst=$this->arcWFirst;
        $boxGap=$this->boxGap;
        $x=$this->startX+$this->boxW+$this->arcWFirst; //定最左上角X座標
        $y=$this->startY+$this->boxH/2; //定最左上角Y座標
        $h=($this->boxH/4);
        $g=$this->boxH+$boxGap;
        $boxH=$this->boxH;
        $arcW=$this->arcW;

        $firstRoundCount=count($winners[0]);
        $ty=$y;
        for($i=0;$i<$firstRoundCount;$i++){
            if($winners[0][$i]==1){
                $this->pdf->Line($x-$arcWFirst, $ty-($boxH/4), $x, $ty-($boxH/4), $this->styleWinnerLine);
            }else if($winners[0][$i]==2){
                $this->pdf->Line($x-$arcWFirst, $ty+($boxH/4), $x, $ty+($boxH/4), $this->styleWinnerLine);
            }
            $ty+=$boxH+$boxGap;
        }

        for($i=0;$i<$this->round;$i++){
            $this->arcWinner($this->pdf, $x, $y, $arcW, $h, $g, $winners[$i]);
            $h=$g/2;
            $x+=$arcW;
            //$y=$this->startY+$g-$boxGap+($this->boxH/4)+($this->arcWFirst/2);
            $y=$this->startY+$g-($this->boxGap/2);
            $g+=$g;
               
        }
    }
    private function repechageChart($totalPlayers, $players){
        $x=$this->startX;
        $y=$this->startY+(($this->boxH+$this->boxGap)*$totalPlayers);
        $this->pdf->Line($x, $y, $x+100, $y, $this->styleResult1); //Repechage horizontal sperate line

        $x1=$this->startX;
        $y1=$y+$this->repechageDistance;
        $boxGap=$this->repechageBoxGap;
        $boxX=$x1;
        $boxY=$y1;
        $arcW=$this->arcW;

        /* Repechage First */
        for($i=0; $i<4; $i+=2){
            $this->boxPlayers($this->pdf, $boxX, $boxY, $this->boxW, $this->boxH, $players[$i]);
            $px=$boxX+$this->boxW;
            $py=$boxY+($this->boxH/4);
            $pW=$this->arcWFirst;
            $pH=$this->boxH /2;
            $this->arcLine($this->pdf,$px, $py, $pW, $pH);
            $boxY+=$this->boxH+$boxGap;
            $this->boxPlayer($this->pdf, $boxX, $boxY, $this->boxW, $this->boxH, $players[$i+1]);
            $boxY+=$this->boxH/2+$this->repechageSectionGap;
        };

        $y1=$y+$this->repechageDistance+($this->boxH/2);
        for($i=0;$i<2;$i++){
            $x1=$x+$this->boxW+$this->arcWFirst;
            $h=$this->boxH/2+$boxGap+$this->boxH/4;
            $this->arcLine($this->pdf, $x1, $y1, $arcW, $h);
            $this->pdf->Line($x1-$this->arcWFirst, $y1+$h, $x1, $y1+$h, $this->styleArcLine); //padding for the 3 player line
            
            /* last horizontal line*/
            $px1=$x1+$this->arcW;
            $py1=$y1+($h/2);
            $px2=$px1+$this->arcW;
            $this->pdf->Line($px1, $py1, $px2, $py1, $this->styleArcLine);
            //$y1=$y+$this->repechageDistance+($this->boxH/2)+($boxGap*2)+($this->boxH*2);
            $y1=$y+$this->repechageDistance+($this->boxH*2)+$this->repechageBoxGap+$this->repechageSectionGap;


        }
    }
    private function repechageWinnerLine($totalPlayers, $winners){
        $x=$this->startX+$this->boxW;
        $y=$this->startY+($this->boxH+$this->boxGap)*$totalPlayers+$this->repechageDistance+($this->boxH/4);
        $arcW=$this->arcW;
        $x1=$x;
        $y1=$y;

        for($i=0;$i<2;$i++){
            if($winners[0][$i]==1){
                $x1=$x;
                $y1=$y;
                $x2=$x1+$this->arcWFirst;
                $y2=$y1;
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);
                
                $x1=$x2;
                $y1=$y2;
                $x2=$x1;
                $y2=$y1+($this->boxH/4);
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);

                $x1=$x2;
                $y1=$y2;
                $x2=$x1+$arcW;
                $y2=$y1;
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);
            }else if($winners[0][$i]==2){
                $x1=$x;
                $y1=$y+($this->boxH/2);
                $x2=$x1+$this->arcWFirst;
                $y2=$y1;
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);

                $x1=$x2;
                $y1=$y2;
                $x2=$x1;
                $y2=$y1-($this->boxH/4);
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);

                $x1=$x2;
                $y1=$y2;
                $x2=$x1+$arcW;
                $y2=$y1;
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);
            }
            $x1=$x;
            $y1=$y+$this->boxH+$this->repechageBoxGap;
            $x2=$x1+$arcW+$this->arcWFirst;
            $y2=$y1;
            $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);

            if($winners[1][$i]==1){
                $x1=$x2;
                $y1=$y+($this->boxH/4);
                $x2=$x1;
                $y2=$y1+($this->boxH/2);
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);
            }else if($winners[1][$i]==2){
                $x1=$x2;
                $y1=$y+$this->boxH+$this->repechageBoxGap;
                $x2=$x1;
                $y2=$y1-($this->repechageBoxGap)-($this->boxH/4);
                $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);
            }
            $x1=$x2;
            $y1=$y2;
            $x2=$x1+$this->arcW;
            $y2=$y1;
            $this->pdf->Line($x1, $y1, $x2, $y2,$this->styleWinnerLine);
            $y+=$this->boxH+$this->repechageBoxGap+($this->boxH/2)+$this->repechageSectionGap;
        }
    }
    private function boxPlayer($pdf, $x, $y, $w, $h, $players=['white'=>['name_display'=>'white'],'blue'=>['name_display'=>'blue']]){
        $r=2.0;
        $pdf->RoundedRect($x, $y, $w, $h/2, $r, '1111', 'DF', $this->styleBoxLine, $this->boxBlueColor);
        $pdf->Text($x, $y, $players['blue']['name_display']);
    }
    /* the following are repeated object, might change the variabled values, accordingly, not suggest to use globale variabled. */
    private function boxPlayers($pdf, $x, $y, $w, $h, $players=['white'=>['name_display'=>'white'],'blue'=>['name_display'=>'blue']]){
        $r=2.0;
        $h=$h/2;
        $pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
        $pdf->RoundedRect($x, $y+$h, $w, $h, $r, '0110', 'DF', $this->styleBoxLine, $this->boxBlueColor);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($this->boxW, $h, $players['white']['name_display'], 0, 1, 'L', 0, '', 0);
        $this->pdf->setXY($x, $y+$h);
        $this->pdf->Cell($this->boxW, $h, $players['blue']['name_display'], 0, 1, 'L', 0, '', 0);
    }
    private function arcLine($pdf, $x, $y, $arcW, $h){
        $style = array('L' => 0,
                        'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                        'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor),
                        'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $this->arcColor));
        $pdf->Rect($x, $y, $arcW, $h, 'D', $style );
    }
    private function arcWinner($pdf, $x, $y, $arcW, $h, $g, $winners){
        $cnt=count($winners);
        for($j=0;$j<$cnt;$j++){
            if($winners[$j]==1){
                $pdf->Line($x, $y, $x+$arcW, $y, $this->styleWinnerLine);
                $pdf->Line($x, $y-$h, $x, $y, $this->styleWinnerLine);
            }else if($winners[$j]==2){
                $pdf->Line($x, $y, $x+$arcW, $y, $this->styleWinnerLine);
                $pdf->Line($x, $y, $x, $y+$h, $this->styleWinnerLine);
            }
            $y+=$g;
        }
    }
    private function sequenceNumbers($sequences){
        $size=$this->circleSize;
        $x=$this->startX+$this->boxW+$this->arcWFirst; //定最左上角X座標
        $y=$this->startY+$this->boxH/2; //定最左上角Y座標
        $x1=$x;
        $y1=$y;
        $baseY=0;
        $step=($this->boxH+$this->boxGap);
        $this->pdf->SetFont('times','',10);
        for($i=0;$i<$this->round;$i++){
            $count=count($sequences[$i]);
            for($j=0;$j<$count;$j++){
                $this->pdf->Ellipse($x1, $y1, $size, 0, 360, 0, 360, 'DF', $this->styleCircle,$this->circleColor);
                $this->pdf->setXY($x1-$size, $y1-$size);
                $this->pdf->Cell($size*2, $size*2, $sequences[$i][$j], 0, 1, 'C', 0, '', 0);
                $y1+=$step;
            }
            $x1=$x+$this->arcW*($i+1);
            $baseY=$step;
            $y1=$y+$baseY-(($this->boxH+$this->boxGap)/2);
            $step*=2;
        }
        
        
        /* Repechage cicle number */
        $x=$this->startX+$this->boxW+$this->arcWFirst;
        $y=$this->startY+(($this->boxH+$this->boxGap)*$this->playerCount)+$this->repechageDistance+$this->boxH/2;
        $x1=$x;
        $y1=$y;
        $baseY=0;
        $step=($this->boxH+$this->boxGap);
        for($i=0; $i<2; $i++){
            for($j=0; $j<2; $j++){
                $this->pdf->Ellipse($x1, $y1, $size, 0, 360, 0, 360, 'DF', $this->styleCircle, $this->circleColor);
                $this->pdf->setXY($x1-$size, $y1-$size);
                $this->pdf->Cell($size*2, $size*2, $sequences[$i+$this->round][$j], 0, 1, 'C', 0, '', 0);
                $y1+=$step+$this->repechageSectionGap+($this->boxH/2);
            }
            $y1=$y+($this->boxH/2);
            $x1+=$this->arcW;
        }
    }    
    private function resultBox($winnerList){
        $x=145;
        $y=$this->startY+($this->boxH+$this->boxGap)* $this->playerCount-$this->boxH;
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
            $this->pdf->SetFont('times', 'B', 10);
            $this->pdf->Cell(0, 0, $winnerList[$i]['award'].':', 0, 1, 'L', 0, '', 0);
            $this->pdf->setXY($x1+15, $y1+5);
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
        $this->pdf->SetFont('times', 'B', 14);
        $this->pdf->Cell(35, 10, 'Results', 0, 1, 'C', 0, '', 0);

    }

}