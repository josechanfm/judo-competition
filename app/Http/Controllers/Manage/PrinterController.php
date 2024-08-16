<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCPDF;

class PrinterController extends Controller
{
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
            ],            [
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
            [1,2,1,2,1,1,1,1],
            [1,2,1,2],
            [1,1],
            [1]
        ];

        $pdf = new TCPDF();
        $pdf->AddPage();
       
        //$players=count($players);
        $startX=20; //starting X axis of the drawing area
        $startY=10; //starting Y axis of the drawing area
        $boxW=45;
        $boxH=12;
        $arcW=15;
        $boxGap=2;

        $this->fullArcs($pdf,$startX, $startY, $arcW, $boxW, $boxH, $players);
        $this->winnerArcs($pdf,$startX, $startY, $arcW, $boxW, $boxH, $winners);
        $pdf->Output('myfile.pdf', 'I');
    }

    private function fullArcs($pdf, $x, $y, $w, $boxW, $boxH, $players){
        /* box padding, arc line */
        $playerCount=count($players);
        $boxGap=2;
        $boxX=$x;
        $boxY=$y;
        $padding=2;
        /* player box */
        for($i=0;$i<$playerCount;$i++){
            $this->boxPlayers($pdf, $boxX, $boxY, $boxW, $boxH, $players[$i]);
            $boxY+=$boxGap+$boxH;
        }

        /* player box padding arc line */
        $l=$boxW+0; //padding space on left
        $px=$x+$l; //starting point of $x axis
        $py=$y+($boxH/4); //starting point of $y axis
        $pW=2; //width of arch shape
        $h=$boxH / 2; //height of arch shape
        $pGap= $h*2+$boxGap; //$h +$boxH+$boxGap; //gap betwee each arch in vertical alignment
        for($i=0;$i<$playerCount;$i++){
            //$this->arcWinner($pdf, $px, $py, $pW, $h, $players[$i]);
            $this->arcLine($pdf,$px, $py, $pW, $h);
            $py+=$pGap;
        }

        /* arch line */        
        $ax=$x+$boxW+$padding;
        $ah=$boxH+$boxGap;
        $ay=$y+($boxH/2);
        $round=strlen((string)decbin($playerCount))-1;
        $cnt=$playerCount/2;
        
        for($i=1;$i<=($round);$i++){
            $arcGap=$ah *2; //gap betwee each arch in vertical alignment
            for($j=0;$j<$cnt;$j++){
                $this->arcLine($pdf,$ax, $ay, $w, $ah);
                $ay+=$arcGap;
            }
            $ax+=$w;
            $ay=$y+$ah-$boxGap+1;
            $ah+=$ah;
            $cnt/=2;
        }
    }
    private function boxPlayers($pdf, $x, $y, $w, $h, $players=['white'=>['name_display'=>'white'],'blue'=>['name_display'=>'blue']]){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $r=2.0;
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect($x-$h, $y, $h, $h, $h/2, '0011', 'D');
        $pdf->RoundedRect($x, $y, $w, $h, $r, '1100', 'D');
        $pdf->Line($x, $y+($h/2), $x+$w, $y+($h/2), $style);
        $pdf->Text($x, $y, $players['white']['name_display']);
        $pdf->Text($x, $y+($h/2), $players['blue']['name_display']);
    }
    private function arcLine($pdf, $x, $y, $w, $h){
        $style = array('L' => 0,
                        'T' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)),
                        'R' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)),
                        'B' => array('width' => 0.10, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)));
        $pdf->Rect($x, $y, $w, $h, 'D', $style );
    }

    private function winnerArcs($pdf, $startX, $startY, $arcW, $boxW, $boxH, $winners){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $round=count($winners);
        $padding=2;
        $boxGap=2;
        $x=$startX+$boxW+$padding;
        $y=$startY+$boxH/2;
        $w=$arcW;
        $h=($boxH/4);
        $g=$boxH+$boxGap;
        $firstRoundCount=count($winners[0]);
        
        $ty=$y;
        for($i=0;$i<$firstRoundCount;$i++){
            $pdf->text($x,$ty,$winners[0][$i]);
            if($winners[0][$i]==1){
                $pdf->Line($x-$padding, $ty-($boxH/4), $x, $ty-($boxH/4), $style);
            }else if($winners[0][$i]==2){
                $pdf->Line($x-$padding, $ty+($boxH/4), $x, $ty+($boxH/4), $style);
            }
            $ty+=$boxH+$boxGap;
        }

        for($i=0;$i<$round;$i++){
            $this->arcWinner($pdf, $x, $y, $w, $h, $g, $winners[$i]);
            $h=$g/2;
            $x+=$arcW;
            $y=$startY+$g-$boxGap+1;
            $g+=$g;
               
        }
    }
    
    private function arcWinner($pdf, $x, $y, $w, $h, $g, $winners){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $cnt=count($winners);
        for($j=0;$j<$cnt;$j++){
            if($winners[$j]==1){
                //$pdf->text($x, $y,$winners[$j]);
                $pdf->Line($x, $y, $x+$w, $y, $style);
                $pdf->Line($x, $y-$h, $x, $y, $style);
            }else if($winners[$j]==2){
                $pdf->Line($x, $y, $x+$w, $y, $style);
                $pdf->Line($x, $y, $x, $y+$h, $style);
                //$pdf->text($x1, $y,$winners[$i][$j]);
                //$pdf->Line($x, $y-$h, $x, $y, $style);
            }
            //$x+=5;
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
