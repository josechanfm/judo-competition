<?php
namespace App\Services;

use TCPDF;

class ProgramScheduleService{
    
    protected $pdf=null;
    protected $gameSetting=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';
    protected $logo_primary='images/jua_logo.png';
    protected $logo_secondary=null;

    protected $startX=15; //面頁基點X軸
    protected $startY=30; //面頁基點Y軸
    protected $boxWhiteColor=array(240,240,255);
    protected $boxBlueColor=array(255,255,255);
    protected $styleBoxLine=null;


    public function __construct(){
        $this->styleBoxLine= array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));

    }
    public function setLogos($primary=null, $secondary=null){
        $this->logo_primary=$primary;
        $this->logo_secondary=$secondary;
    }

    public function setTitles($title=null, $title_sub=null){
        $this->title=$title;
        $this->title_sub=$title_sub;
    }

    public function pdf($records){


        $this->pdf = new \Mpdf\Mpdf();
        //$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // $this->pdf->SetPrintHeader(false);
        // $this->pdf->SetMargins(15,10,15);
        // $this->pdf->SetAutoPageBreak(TRUE,0);
        $this->pdf->AddPage();

        $this->header();        
        $this->schedule($records);
        $this->pdf->Output('myfile.pdf', 'I');
    }
    public function header(){
        $x=10;
        $y=5;
        $w=185;
        $h=14;
        $r=5;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->styleBoxLine, $this->boxWhiteColor);
        if($this->logo_primary){
            $this->pdf->image($this->logo_primary,$x+2, $y+2, 10,10,'png');
        }
        if($this->logo_secondary){
            $this->pdf->image($this->logo_secondary,$x+$w-13, $y+2, 10,10,'png');
        }
        
        $x=25;
        $w=165;
        $this->pdf->setFont('times','B',16);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($w, $h/1.6, $this->title, 0, 1, 'C', 0, '', 0);
        $this->pdf->setFont('times','B',11);
        $this->pdf->setXY($x, $y+($h/1.6));
        $this->pdf->Cell($w, $h-($h/1.6 ), $this->title_sub, 0, 0, 'C', 0, '', 0);
    }

    public function schedule($records){
        $this->pdf->setXY($this->startX, $this->startY);
        $data ='
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        foreach($records as $record){
            $data .='<table class="tableMain">';
            $data .='<tr><td>';
            $data .='<table class="tableLeft">';
            $data .='<tr>';
            $data .='<td rowspan="4" style="width:42px;border:none!important"><div class="arc"></div>'.$record['sequence'].'</td>';
            $data .='<td rowspan="4">'.$record['weight'].'<br><font style="font-size:14;font-weight:bold">'.$record['category'].'</font><br><font style="font-size:12;font-weight:bold">'.$record['round'].'</fong></td>';
            $data .='<td rowspan="2" style="width:150px">'.$record['white_player'].'</td>';
            $data .='<td rowspan="2" style="width:150px">'.$record['white_team'].'</td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='</tr>';
            $data .='<tr>';
            $data .='<td rowspan="2">I</td>';
            $data .='<td rowspan="2">W</td>';
            $data .='<td rowspan="2">S</td>';
            $data .='<td rowspan="2">'.$record['time'].'</td>';
            $data .='</tr>';
            $data .='<tr>';
            $data .='<td rowspan="2" style="background:lightblue">'.$record['blue_player'].'</td>';
            $data .='<td rowspan="2" style="background:lightblue">'.$record['blue_team'].'</td>';
            $data .='</tr>';
            $data .='<tr>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='</tr>';
            $data .='</table>';
            $data .='</td>';
            $data .='<td style="width:5px"></td>';
            $data .='<td>'.$rightTable.'</td>';
            $data .='</tr></table>';
            $data .='<div class="spacer"></div>';
        }
        // $mpdf = new \Mpdf\Mpdf();
        // $mpdf->WriteHTML($data);
        // $mpdf->Output('myfile.pdf', 'I');
        $this->pdf->WriteHTML($data);
        $this->pdf->Output('myfile.pdf','I');
    }

}