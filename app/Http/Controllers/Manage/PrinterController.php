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
            [1,1,1,1,1,1,1,1],
            [1,1,1,1],
            [1,1],
            [1]
        ];

        $pdf = new TCPDF();
        $pdf->AddPage();
       
        //$players=count($players);
        $startX=20;
        $startY=10;
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
            $ay=$y+$ah-$boxGap;
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
        $x1=$startX+$boxW+$padding;
        $x2=$startX+$boxW+$arcW+$padding;
        $startY=$startY+($boxH/4);
        //$y2=$startY+($boxH/4);
        for($i=0;$i<$round;$i++){
            if($i==0){
                $y=$startY+($boxH/4);
            }else{
                $startY=$startY+($boxH/4)+($boxH/2);
            }
            $cnt=count($winners[$i]);
            for($j=0;$j<$cnt;$j++){
                if($winners[$i][$j]==1){
                    $pdf->text($x1, $y,$winners[$i][$j]);
                    $pdf->Line($x1, $y, $x2, $y, $style);
                }else if($winners[$i][$j]==2){
                    //$pdf->text($x1, $y,$winners[$i][$j]);
                    //$pdf->Line($x1, $y+($boxH/2), $x2, $y+($boxH/2), $style);
                }
                $y+=($boxH+$boxGap);
            }
            $x1+=$arcW;
            //$x2+=$x1+$arcW;
        }
    }

    
    private function arcWinner($pdf, $x1, $y1, $w, $h, $winner='',$firstRound=false){
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $pdf->text($x1,$y1, $w);
        if($firstRound){
            if($winner==1){
                //$pdf->Line($x1, $y1, $x1+$w, $y1, $style);
    
            }else if($winner==2){
                //$pdf->Line($x1, $y1+$h, $x1+$w, $y1+$h, $style);
            }
        }
        if($winner==1){
            $pdf->Line($x1, $y1-($h/2), $x1+5, $y1+($h/2), $style);
            $pdf->Line($x1, $y1, $x1+$w, $y1, $style);
            
            //$pdf->Line($x2+$w, $y1, $x+($w*2), $y+($h/2), $style);
   
        }else if($winner==2){
            //$pdf->Line($x1+$w, $y1+$w, 100, 200, $style);
            //$pdf->Line($x+$w, $y+($h/2), $x+$w, $y+($h/2), $style);
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
