<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCPDF;

class PrinterController extends Controller
{
    protected $pdf=null;
    protected $startX=20;
    protected $startY=10;
    protected $boxW=45;
    protected $boxH=8;
    protected $boxGap=2; //vertical gap space between each player box
    protected $repechageBoxGap=4;
    protected $arcW=15;
    protected $arcWFirst=6;
    protected $repechage='QUARTER'; //KNOCKOUT, FULL, QUARTER, DOUBLE, TWOTHIRD, CONSOLATION

    public function htmlToPdf(){
        $players=[
            [
                'white'=>['name_display'=>'White player 1'],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ],[
                'white'=>['name_display'=>'White player 5'],
                'blue'=>['name_display'=>'Blue player 6'],
            ],[
                'white'=>['name_display'=>'White player 7'],
                'blue'=>['name_display'=>'Blue player 8'],
            ],[
                'white'=>['name_display'=>'White player 9'],
                'blue'=>['name_display'=>'Blue player 10'],
            ],[
                'white'=>['name_display'=>'White player 11'],
                'blue'=>['name_display'=>'Blue player 12'],
            ],[
                'white'=>['name_display'=>'White player 13'],
                'blue'=>['name_display'=>'Blue player 14'],
            ],[
                'white'=>['name_display'=>'White player 15'],
                'blue'=>['name_display'=>'Blue player 16'],
            ],[
                'white'=>['name_display'=>'White player 1'],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ],[
                'white'=>['name_display'=>'White player 5'],
                'blue'=>['name_display'=>'Blue player 6'],
            ],[
                'white'=>['name_display'=>'White player 7'],
                'blue'=>['name_display'=>'Blue player 8'],
            ],[
                'white'=>['name_display'=>'White player 9'],
                'blue'=>['name_display'=>'Blue player 10'],
            ],[
                'white'=>['name_display'=>'White player 11'],
                'blue'=>['name_display'=>'Blue player 12'],
            ],[
                'white'=>['name_display'=>'White player 13'],
                'blue'=>['name_display'=>'Blue player 14'],
            ],[
                'white'=>['name_display'=>'White player 15'],
                'blue'=>['name_display'=>'Blue player 16'],
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [1,2,1,2,1,2,1,2],
            [0,0,0,0],
            [0,0],
            [0]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'white'=>['name_display'=>'White player --'],
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'white'=>['name_display'=>'White player --'],
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [2,1],
            [2,2],
            [1]
        ];
        $this->pdf = new TCPDF();
        $this->pdf->AddPage();
      
        $this->mainChart($players);
        $this->winnerLine($winners);
        $this->repechageChart(count($players),$repechagePlayers);
        $this->repechageWinnerLine(count($players),$repechageWinners);
        $this->pdf->Output('myfile.pdf', 'I');
    }
    private function mainChart($players){
        /* box padding, arc line */
        $playerCount=count($players);
        $boxX=$this->startX; //box X axis, do prevent the change of startX original value,
        $boxY=$this->startY; //box Y axis, do prevent the change of the startY original value,
        $arcWFirst=$this->arcWFirst;
        /* player box */
        for($i=0;$i<$playerCount;$i++){
            $this->boxPlayers($this->pdf, $boxX, $boxY, $this->boxW, $this->boxH, $players[$i]);
            $boxY+=$this->boxGap+$this->boxH; //accumulated for the text box starting point
        }

        /* player box padding arc line */
        $px=$this->startX+$this->boxW; //starting point of $x axis
        $py=$this->startY+($this->boxH/4); //starting point of $y axis
        $pW=$this->arcWFirst; //width of arch shape
        $h=$this->boxH / 2; //height of arch shape
        $pGap= $h*2+$this->boxGap; //$h +$boxH+$boxGap; //gap betwee each arch in vertical alignment
        for($i=0;$i<$playerCount;$i++){
            //$this->arcWinner($pdf, $px, $py, $pW, $h, $players[$i]);
            $this->arcLine($this->pdf,$px, $py, $pW, $h);
            $py+=$pGap;
        }

        /* arch line */        
        $ax=$this->startX+$this->boxW+$arcWFirst;
        $ah=$this->boxH+$this->boxGap;
        $ay=$this->startY+($this->boxH/2);
        $round=strlen((string)decbin($playerCount))-1;
        $cnt=$playerCount/2;
        
        for($i=1;$i<=($round);$i++){
            $arcGap=$ah *2; //gap betwee each arch in vertical alignment
            for($j=0;$j<$cnt;$j++){
                $this->arcLine($this->pdf,$ax, $ay, $this->arcW, $ah);
                $ay+=$arcGap;
            }
            $ax+=$this->arcW;
            $ay=$this->startY+$ah-$this->boxGap+($this->boxH/4)+($this->arcWFirst/2);
            $ah+=$ah;
            $cnt/=2;
        }
    }
    private function winnerLine($winners){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $round=count($winners);
        $arcWFirst=$this->arcWFirst;
        $boxGap=$this->boxGap;
        $x=$this->startX+$this->boxW+$this->arcWFirst;
        $y=$this->startY+$this->boxH/2;
        $w=$this->arcW;
        $h=($this->boxH/4);
        $g=$this->boxH+$boxGap;
        $boxH=$this->boxH;
        $arcW=$this->arcW;

        $firstRoundCount=count($winners[0]);
        
        $ty=$y;
        for($i=0;$i<$firstRoundCount;$i++){
            $this->pdf->text($x,$ty,$winners[0][$i]);
            if($winners[0][$i]==1){
                $this->pdf->Line($x-$arcWFirst, $ty-($boxH/4), $x, $ty-($boxH/4), $style);
            }else if($winners[0][$i]==2){
                $this->pdf->Line($x-$arcWFirst, $ty+($boxH/4), $x, $ty+($boxH/4), $style);
            }
            $ty+=$boxH+$boxGap;
        }
        for($i=0;$i<$round;$i++){
            $this->arcWinner($this->pdf, $x, $y, $arcW, $h, $g, $winners[$i]);
            $h=$g/2;
            $x+=$arcW;
            $y=$this->startY+$g-$boxGap+($this->boxH/4)+($this->arcWFirst/2);
            $g+=$g;
               
        }
    }
    private function repechageChart($totalPlayers, $players){
        $style = array('width' => 0.10, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50, 127));
        $x=$this->startX;
        $y=$this->startY+($this->boxH+$this->boxGap)*$totalPlayers;

        $x1=$this->startX;
        $y1=$y;
        $x2=$this->startX+100;
        $y2=$y1;
        $this->pdf->Line($x1, $y1, $x2, $y2, $style);
        $boxGap=$this->repechageBoxGap;
        $boxX=$x1;
        $boxY=$y1+$boxGap;
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
            $boxY+=$this->boxH+$boxGap;
        };

        $y1=$y+$boxGap+($this->boxH/2);
        for($i=0;$i<2;$i++){
            $x1=$this->startX+$this->boxW+$this->arcWFirst;
            $h=$this->boxH/2+$boxGap+$this->boxH/4;
            $this->arcLine($this->pdf, $x1, $y1, $arcW, $h);
            $this->pdf->Line($x1-$this->arcWFirst, $y1+$h, $x1, $y1+$h, $style);
            $x1+=$this->arcW;
            $x2=$x1+$this->arcW;
            $y1+=$boxGap+($this->boxH/4);
            $this->pdf->Line($x1, $y1, $x2, $y1, $style);
            $y1=$y+$boxGap+($this->boxH/2)+$h+($boxGap*4)+($this->boxH/4);
        }
    }
    private function repechageWinnerLine($totalPlayers, $winners){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $x=$this->startX+$this->boxW+$this->arcWFirst;
        $y=$this->startY+($this->boxH+$this->boxGap)*$totalPlayers;
        $repechageGap=$this->repechageBoxGap*3;

        $x1=$x-$this->arcWFirst;
        $y1=$y+$this->boxH/2+$this->repechageBoxGap/4;
        $x2=$x;
        $y2=$y1;
        for($i=0;$i<2;$i++){
            if($winners[0][$i]==1){
                $this->pdf->text($x1, $y1,$winners[0][$i]);
                $this->pdf->Line($x1, $y1, $x2, $y2,$style);
                $this->pdf->Line($x1+$this->arcWFirst, $y2, $x2, $y2+($this->arcWFirst/2),$style);
                $y1+=($this->boxH+$this->repechageBoxGap)*2;
                $y2=$y1;
            }else if($winners[0][$i]==2){
                $y1+=$this->boxH/2;
                $y2=$y1;
                $this->pdf->text($x1, $y1,$winners[0][$i]);
                $this->pdf->Line($x1, $y1, $x2, $y2,$style);
                $this->pdf->Line($x1+$this->arcWFirst, $y2, $x2, $y2-($this->arcWFirst/2),$style);
                $y1+=($this->boxH+$this->repechageBoxGap)*2;
                $y2=$y1;
            }
            $y1+=10;
        }

        $arcW=$this->arcW;
        $x1=$x;
        $y1=$y+$this->repechageBoxGap+($this->boxH/2);
        $x2=$x1+$arcW;
        $y2=$y1;
        for($i=0;$i<2;$i++){
            $this->repechageArcWinner($x1, $y1, $x2, $y2, $winners[1][$i]);
            $x1=$x;
            $y1=$y+($this->repechageBoxGap+$this->boxH) *3 + ($this->repechageBoxGap/2);
            $x2=$x1+$arcW;
            $y2=$y1;
            $y1=$y2-($this->boxH/2)-($repechageGap/6);
            $y2=$y1;
        }
    }
    private function repechageArcWinner($x1, $y1, $x2, $y2, $winner){
        $style = array('width' => 0.50, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        if($winner==1){
            $this->pdf->Line($x1, $y1, $x2, $y2, $style);
            $x1=$x2;
            $y1=$y2;
            //$y2=$y1+($this->boxH+$this->repechageBoxGap)/2;
            $y2=$y1+($this->boxH/2)+($this->repechageBoxGap/4);
            $this->pdf->Line($x1, $y1, $x2, $y2, $style);
            $x1=$x2;
            $x2=$x1+$this->arcW;
            $y1=$y2;
            $this->pdf->Line($x1, $y1, $x2, $y2, $style);
        }else if($winner==2){
            $y1=$y2+($this->boxH)+($this->repechageBoxGap/4);
            $y2=$y1;
            $this->pdf->Line($x1, $y1, $x2, $y2, $style);
            $this->pdf->Line($x1-$this->arcWFirst, $y1, $x2, $y2, $style);
            $x1=$x2;
            $y1=$y2;
            $x2=$x1;
            $y2=$y2-($this->boxH/2);
            $this->pdf->Line($x1, $y1, $x2, $y2, $style);
            $x1=$x2;
            $y1=$y2;
            $x2=$x1+$this->arcW;
            $y2=$y1;
            $this->pdf->Line($x1, $y1, $x2, $y2, $style);
        }

    }
    private function boxPlayer($pdf, $x, $y, $w, $h, $players=['white'=>['name_display'=>'white'],'blue'=>['name_display'=>'blue']]){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $r=2.0;
        $pdf->RoundedRect($x, $y, $w, $h/2, $r, '1111', 'D');
        $pdf->Text($x, $y, $players['blue']['name_display']);
    }
    /* the following are repeated object, might change the variabled values, accordingly, not suggest to use globale variabled. */
    private function boxPlayers($pdf, $x, $y, $w, $h, $players=['white'=>['name_display'=>'white'],'blue'=>['name_display'=>'blue']]){
        $style = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $r=2.0;
        //$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //$pdf->RoundedRect($x-$h, $y, $h, $h, $h/2, '0011', 'D');
        $pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'D');
        $pdf->Line($x, $y+($h/2), $x+$w, $y+($h/2), $style);
        $pdf->Text($x, $y, $players['white']['name_display']);
        $pdf->Text($x, $y+($h/2), $players['blue']['name_display']);
    }
    private function arcLine($pdf, $x, $y, $arcW, $h){
        $style = array('L' => 0,
                        'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)),
                        'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)),
                        'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)));
        $pdf->Rect($x, $y, $arcW, $h, 'D', $style );
    }
    private function arcWinner($pdf, $x, $y, $arcW, $h, $g, $winners){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $cnt=count($winners);
        for($j=0;$j<$cnt;$j++){
            if($winners[$j]==1){
                $pdf->Line($x, $y, $x+$arcW, $y, $style);
                $pdf->Line($x, $y-$h, $x, $y, $style);
            }else if($winners[$j]==2){
                $pdf->Line($x, $y, $x+$arcW, $y, $style);
                $pdf->Line($x, $y, $x, $y+$h, $style);
            }
            $y+=$g;
        }
    }
    

    public function programs(){
        $rows=[
            [
                'category'=>'CAT A',
                'weight'=>'56kg-',
                'round'=>'Round 5',
                'eventVolunteerId'=>'ev001',
                'eventTitle'=>'Event Title',
                'rollno'=>'R123',
                'eventDate'=>'2024-01-31',
                'eventTime'=>'18:30',
                'eventLimit'=>'3'
            ],[
                'category'=>'CAT B',
                'weight'=>'66kg-',
                'round'=>'Round 5',
                'eventVolunteerId'=>'ev001',
                'eventTitle'=>'Event Title',
                'rollno'=>'R123',
                'eventDate'=>'2024-01-31',
                'eventTime'=>'18:30',
                'eventLimit'=>'3'
            ]

        ];
        $data ='
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>My PDF</title>
                <style> 
                .tableMain{
                    width:100%;
                    display: inline-block;
                    border-collapse: collapse;
                }
                .tableMain td{
                    height:18px;
                    text-align: center;
                    padding: 0px;
                    border-collapse: collapse;
                    vertical-align: middle;
                }
                .tableRight{
                    width: 300px;
                    border-collapse: collapse;
                }
                .tableLeft {
                    float:left;
                    width: 550px;
                    border-collapse: collapse;
                }
                .tableLeft td, .tableRight td  {
                    text-align: center;
                    vertical-align: middle;
                    border: 1px solid #000; 
                    border-radius: 10px;
                }
                .arc{
                    position: absolute;
                    display: inline-block;
                    height: 55px;
                    width: 40px;
                    border-radius: 100% / 100% 0% 0% 100%;
                    border: 1px solid #000;
                    color: #fff;
                    text-align: center;
                }
                .spacer{
                    height:10px;
                }
                </style>
            </head>
        ';
        $rightTable='
        <div style="flow:right">
        <table class="tableRight">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </div>
        ';
        foreach($rows as $row){
            $data .='<table class="tableMain">';
            $data .='<tr><td>';
            $data .='<table class="tableLeft">';
            $data .='<tr>';
            $data .='<td rowspan="4" style="width:42px;border:none!important"><div class="arc"></div>A11</td>';
            $data .='<td rowspan="4">'.$row['weight'].'<br><font style="font-size:14;font-weight:bold">'.$row['category'].'</font><br><font style="font-size:12;font-weight:bold">'.$row['round'].'</fong></td>';
            $data .='<td rowspan="2" style="width:150px">a3</td>';
            $data .='<td rowspan="2" style="width:150px">a4</td>';
            $data .='<td>a5</td>';
            $data .='<td>a6</td>';
            $data .='<td>a7</td>';
            $data .='<td>a8</td>';
            $data .='</tr>';
            $data .='<tr>';
            $data .='<td rowspan="2">I</td>';
            $data .='<td rowspan="2">W</td>';
            $data .='<td rowspan="2">S</td>';
            $data .='<td rowspan="2">02:00</td>';
            $data .='</tr>';
            $data .='<tr>';
            $data .='<td rowspan="2" style="background:lightblue">c3</td>';
            $data .='<td rowspan="2" style="background:lightblue">c4</td>';
            $data .='</tr>';
            $data .='<tr>';
            $data .='<td>d5</td>';
            $data .='<td>d6</td>';
            $data .='<td>d7</td>';
            $data .='<td>d8</td>';
            $data .='</tr>';
            $data .='</table>';
            $data .='</td>';
            $data .='<td style="width:5px"></td>';
            $data .='<td>'.$rightTable.'</td>';
            $data .='</tr></table>';
            $data .='<div class="spacer"></div>';
        }
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($data);
        $mpdf->Output('myfile.pdf', 'I');
    }
}
