<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TournamentChartService;

class PrinterController extends Controller
{
    public function htmlToPdf(Request $request){
        if(empty($request->size)){
            $this->players32();
            return true;
        }
        switch($request->size){
            case 4:
                $this->players4();
            case 8:
                $this->players8();
            case 16:
                $this->players16();
            case 32:
                $this->players32();
            case 64:
                $this->players64();
        }
    }

    private function players4(){
        $players=[
            [
                'white'=>['name_display'=>'White player 1','status'=>false],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ]
        ];
        $winners=[
            [1,2],
            [2]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [1,2],
            [1,2],
        ];
        $sequences=[
            [1,2],
            [3],
            [4,5],
            [6,7],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $tournament=new TournamentChartService;
        $tournament->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);

    }
    private function players8(){
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
            ]
        ];
        $winners=[
            [2,1,2,1],
            [1,2],
            [2]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [1,2],
            [1,2],
        ];
        $sequences=[
            [1,2,3,4],
            [5,6],
            [7],
            [8,9],
            [10,11],
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $tournament=new TournamentChartService;
        $tournament->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);
       
    }
    private function players16(){
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
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2],
            [2,1,2,1],
            [1,2],
            [2]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [1,2],
            [1,2],
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8],
            [9,10,11,12],
            [13,14],
            [15],
            [16,17],
            [18,19]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $tournament=new TournamentChartService;
        $tournament->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);
    }
    private function players32(){
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
                'white'=>['name_display'=>'White player 17'],
                'blue'=>['name_display'=>'Blue player 18'],
            ],[
                'white'=>['name_display'=>'White player 19'],
                'blue'=>['name_display'=>'Blue player 20'],
            ],[
                'white'=>['name_display'=>'White player 21'],
                'blue'=>['name_display'=>'Blue player 22'],
            ],[
                'white'=>['name_display'=>'White player 23'],
                'blue'=>['name_display'=>'Blue player 24'],
            ],[
                'white'=>['name_display'=>'White player 25'],
                'blue'=>['name_display'=>'Blue player 26'],
            ],[
                'white'=>['name_display'=>'White player 27'],
                'blue'=>['name_display'=>'Blue player 28'],
            ],[
                'white'=>['name_display'=>'White player 29'],
                'blue'=>['name_display'=>'Blue player 30'],
            ],[
                'white'=>['name_display'=>'White player 31'],
                'blue'=>['name_display'=>'Blue player 32'],
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [2,1,2,1,1,2,1,2],
            [1,2,1,2],
            [2,1],
            [1]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [1,2],
            [1,2],
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
            [17,18,19,20,21,22,23,24],
            [24,26,27,28],
            [29,30],
            [31],
            [32,33],
            [34,35]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $tournament=new TournamentChartService;
        $tournament->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);
    }
    private function players64(){
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
                'white'=>['name_display'=>'White player 17'],
                'blue'=>['name_display'=>'Blue player 18'],
            ],[
                'white'=>['name_display'=>'White player 19'],
                'blue'=>['name_display'=>'Blue player 20'],
            ],[
                'white'=>['name_display'=>'White player 21'],
                'blue'=>['name_display'=>'Blue player 22'],
            ],[
                'white'=>['name_display'=>'White player 23'],
                'blue'=>['name_display'=>'Blue player 24'],
            ],[
                'white'=>['name_display'=>'White player 25'],
                'blue'=>['name_display'=>'Blue player 26'],
            ],[
                'white'=>['name_display'=>'White player 27'],
                'blue'=>['name_display'=>'Blue player 28'],
            ],[
                'white'=>['name_display'=>'White player 29'],
                'blue'=>['name_display'=>'Blue player 30'],
            ],[
                'white'=>['name_display'=>'White player 31'],
                'blue'=>['name_display'=>'Blue player 32'],
            ],[
                'white'=>['name_display'=>'White player 33'],
                'blue'=>['name_display'=>'Blue player 34'],
            ],[
                'white'=>['name_display'=>'White player 35'],
                'blue'=>['name_display'=>'Blue player 36'],
            ],[
                'white'=>['name_display'=>'White player 37'],
                'blue'=>['name_display'=>'Blue player 38'],
            ],[
                'white'=>['name_display'=>'White player 39'],
                'blue'=>['name_display'=>'Blue player 40'],
            ],[
                'white'=>['name_display'=>'White player 41'],
                'blue'=>['name_display'=>'Blue player 42'],
            ],[
                'white'=>['name_display'=>'White player 43'],
                'blue'=>['name_display'=>'Blue player 44'],
            ],[
                'white'=>['name_display'=>'White player 45'],
                'blue'=>['name_display'=>'Blue player 46'],
            ],[
                'white'=>['name_display'=>'White player 47'],
                'blue'=>['name_display'=>'Blue player 48'],
            ],[
                'white'=>['name_display'=>'White player 49'],
                'blue'=>['name_display'=>'Blue player 50'],
            ],[
                'white'=>['name_display'=>'White player 51'],
                'blue'=>['name_display'=>'Blue player 52'],
            ],[
                'white'=>['name_display'=>'White player 53'],
                'blue'=>['name_display'=>'Blue player 54'],
            ],[
                'white'=>['name_display'=>'White player 55'],
                'blue'=>['name_display'=>'Blue player 56'],
            ],[
                'white'=>['name_display'=>'White player 57'],
                'blue'=>['name_display'=>'Blue player 58'],
            ],[
                'white'=>['name_display'=>'White player 59'],
                'blue'=>['name_display'=>'Blue player 60'],
            ],[
                'white'=>['name_display'=>'White player 61'],
                'blue'=>['name_display'=>'Blue player 62'],
            ],[
                'white'=>['name_display'=>'White player 31'],
                'blue'=>['name_display'=>'Blue player 32'],
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [1,2,1,2,1,2,1,2,1,2,1,2,1,2,1,2],
            [2,1,2,1,1,2,1,2],
            [1,2,1,2],
            [2,1],
            [1]
        ];
        $repechagePlayers=[
            [
                'white'=>['name_display'=>'White player r1'],
                'blue'=>['name_display'=>'Blue player r2'],
            ],[
                'blue'=>['name_display'=>'Blue player r5'],
            ],[
                'white'=>['name_display'=>'White player r3'],
                'blue'=>['name_display'=>'Blue player r4'],
            ],[
                'blue'=>['name_display'=>'Blue player r6'],
            ]
        ];
        $repechageWinners=[
            [1,2],
            [1,2],
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,24,26,27,28,29,30,31,32],
            [33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48],
            [49,50,51,52,53,54,55,56],
            [57,58,59,60],
            [61,62],
            [63],
            [64,65],
            [67,66]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        $tournament=new TournamentChartService;
        $tournament->pdf($players, $winners, $repechagePlayers, $repechageWinners, $sequences, $winnerList);
        
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
