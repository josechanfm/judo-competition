<?php

namespace App\Helpers;

class PdfHelper{

    protected $pdf=null;
    protected $boxLine= array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
    protected $boxColor=array(240,240,255);
    protected $boxColor2=array(197,192,139);

    public function __construct($pdf){
        $this->pdf=$pdf;
    }

    public function header1($x=0, $y=0, $title=null, $title_sub=null, $logo_primary=null, $logo_secondary=null)
    {
        $w=185;
        $h=14;
        $r=5;
        if(get_class($this->pdf)=='Mpdf\Mpdf'){
            $this->pdf->SetLineWidth(0.2);
            $this->pdf->SetFillColor($this->boxColor[0],$this->boxColor[1],$this->boxColor[2]);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, 'DF');
        }else{
             $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->boxLine, $this->boxColor);
        }
        if($logo_primary){
            $this->pdf->image($logo_primary,$x+2, $y+2, 10,10,'png');
        }
        if($logo_secondary){
            $this->pdf->image($logo_secondary,$x+$w-13, $y+2, 10,10,'png');
        }
        // $x=25;
        // $w=165;
        $this->pdf->setFont('times','B',16);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($w, $h/1.6, $title, 0, 1, 'C', 0, '', 0);
        $this->pdf->setFont('times','B',11);
        $this->pdf->setXY($x, $y+($h/1.6));
        $this->pdf->Cell($w, $h-($h/1.6 ), $title_sub, 0, 0, 'C', 0, '', 0);

    }
    public function header2($x=0, $y=0, $title=null, $title_sub=null, $logo_primary=null, $logo_secondary=null, $extra=null){
        $x=10;
        $y=5;
        $w=180;
        $h=14;
        $r=5;
        if(get_class($this->pdf)=='Mpdf\Mpdf'){
            $this->pdf->SetLineWidth(0.2);
            $this->pdf->SetFillColor($this->boxColor[0],$this->boxColor[1],$this->boxColor[2]);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, 'DF');
    
        }else{
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->boxLine, $this->boxColor);
        }
        if($logo_primary){
            $this->pdf->image($logo_primary,$x+2, $y+2, 10,10,'png');
        }
        if($logo_secondary){
            $this->pdf->image($logo_secondary,$x+$w-13, $y+2, 10,10,'png');
        }
        
        $x=45;
        $w=115;
        $this->pdf->setFont('times','B',18);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($w, $h/1.6, $title, 0, 1, 'C', 0, '', 0);
        $this->pdf->setFont('times','B',11);
        $this->pdf->setXY($x, $y+($h/1.6));
        $this->pdf->Cell($w, $h-($h/1.6 ), $title_sub, 0, 0, 'C', 0, '', 0);
        if($extra){
            $x=160;
            $w=43;
            $h=20;
            $r=9;
            if(get_class($this->pdf)=='Mpdf\Mpdf'){
                $this->pdf->SetLineWidth(0.2);
                $this->pdf->SetFillColor($this->boxColor2[0],$this->boxColor2[1],$this->boxColor2[2]);    
                $this->pdf->RoundedRect($x, $y, $w, $h, $r, 'DF', $this->boxLine, array(197,192,139));
                $this->pdf->setFont('courier','B',28);
                $this->pdf->setXY($x, $y+6);
                $this->pdf->Cell($w, 0, $extra['title'], 0, 0, 'C', 0, '', 0);
                $this->pdf->setFont('times','',12);
                $this->pdf->setXY($x, $y+15);
                $this->pdf->Cell($w, 0, $extra['title_sub'], 0, 0, 'C', 0, '', 0);
            }else{
                $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->boxLine, array(197,192,139));
                $this->pdf->setFont('courier','B',35);
                $this->pdf->setXY($x, $y);
                $this->pdf->Cell($w, 0, $extra['title'], 0, 0, 'C', 0, '', 0);
                $this->pdf->setFont('times','B',16);
                $this->pdf->setXY($x, $y+12);
                $this->pdf->Cell($w, 0, $extra['title_sub'], 0, 0, 'C', 0, '', 0);
                }
        }
    }
    /*
    public function header3($x=0, $y=0, $title=null, $title_sub=null, $logo_primary=null, $logo_secondary=null, $extra=null){
        $x=10;
        $y=5;
        $w=180;
        $h=14;
        $r=5;
        if(get_class($this->pdf)=='Mpdf\Mpdf'){
            $this->pdf->SetLineWidth(0.2);
            $this->pdf->SetFillColor($this->boxColor[0],$this->boxColor[1],$this->boxColor[2]);
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, 'DF');    
        }else{
            $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->boxLine, $this->boxColor);
        }
        if($logo_primary){
            $this->pdf->image($logo_primary,$x+2, $y+2, 10,10,'png');
        }
        if($logo_secondary){
            $this->pdf->image($logo_secondary,$x+$w-13, $y+2, 10,10,'png');
        }
        
        $x=45;
        $w=115;
        $this->pdf->setFont('times','B',16);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($w, $h/1.6, $title, 0, 1, 'C', 0, '', 0);
        $this->pdf->setFont('times','B',11);
        $this->pdf->setXY($x, $y+($h/1.6));
        $this->pdf->Cell($w, $h-($h/1.6 ), $title_sub, 0, 0, 'C', 0, '', 0);
        if($extra){
            $x=165;
            $w=35;
            $h=20;
            $r=9;
            if(get_class($this->pdf)=='Mpdf\Mpdf'){
                $this->pdf->SetLineWidth(0.2);
                $this->pdf->SetFillColor($this->boxColor2[0],$this->boxColor2[1],$this->boxColor2[2]);    
                $this->pdf->RoundedRect($x, $y, $w, $h, $r, 'DF', $this->boxLine, array(197,192,139));
            }else{
                $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $this->boxLine, $this->boxColor2);                
            }
            $this->pdf->setFont('courier','B',25);
            $this->pdf->setXY($x, $y+8);
            $this->pdf->Cell($w, 0, $extra['title'], 0, 0, 'C', 0, '', 0);
            $this->pdf->setFont('times','B',8);
            $this->pdf->setXY($x, $y+16);
            $this->pdf->Cell($w, 0, $extra['title_sub'], 0, 0, 'C', 0, '', 0);
        }
    }
    */

}
