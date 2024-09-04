<?php

namespace App\Helpers;

class PdfHelper{
    protected $pdf=null;

    public function __construct($pdf){
        $this->pdf=$pdf;
    }

    public function header($x=0, $y=0, $title=null, $title_sub=null, $logo_primary=null, $logo_secondary=null)
    {
        $boxLine= array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        $boxColor=array(240,240,255);
        $w=185;
        $h=14;
        $r=5;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1111', 'DF', $boxLine, $boxColor);
        $this->pdf->image($logo_primary,$x+2, $y+2, 10,10,'png');
        $this->pdf->image($logo_secondary,$x+$w-13, $y+2, 10,10,'png');
        
        // $x=25;
        // $w=165;
        $this->pdf->setFont('times','B',16);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($w, $h/1.6, $title, 0, 1, 'C', 0, '', 0);
        $this->pdf->setFont('times','B',11);
        $this->pdf->setXY($x, $y+($h/1.6));
        $this->pdf->Cell($w, $h-($h/1.6 ), $title_sub, 0, 0, 'C', 0, '', 0);

    }
}
