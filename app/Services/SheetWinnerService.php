<?php
namespace App\Services;

use TCPDF;

class SheetWinnerService{
    
    protected $pdf=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';

    protected $startX=25; //面頁基點X軸
    protected $startY=30; //面頁基點Y軸

    protected $titleFont='times';
    protected $playerFont='times';
    protected $generalFont='times';

    public function __construct($settings){
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

    public function pdf($winnerList=[]){
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
        
        
        //$this->columns();
        $this->printResult($winnerList);
        $this->pdf->Output('myfile.pdf', 'I');
    }
    public function columns(){
        $left_column = '<b>LEFT COLUMN</b> left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column left column';

        $right_column = '<b>RIGHT COLUMN</b> right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column right column';
        $this->pdf->writeHTMLCell(80, '', '', 20, $left_column, 1, 0, 0, true, 'J', true);
        $this->pdf->writeHTMLCell(80, '', '', '', $right_column, 1, 1, 0, true, 'J', true);

    }
    public function printResult($winnerList){
        //$this->pdf->setXY($this->startX, $this->startY);
        $x=$this->startX;
        $y=$this->startY;
        $leftX=10;
        $rightX=100;
        for($i=0;$i<count($winnerList);$i=$i+2){
            $this->printTable($leftX, $y, $winnerList[$i]);
            $this->printTable($rightX, $y, $winnerList[$i]);
            $y+=70;
        }
        //$this->pdf->writeHTML($html, true, false, false, false, '');

    }
    public function printTable($x, $y, $list){
        $htmlStyle='
        <style>
        table.roundedCorners { 
            border: none ;
            border-radius: 13px; 
            border-spacing: 0;
            }
          table.roundedCorners td, 
          table.roundedCorners th { 
            border: 1px solid DarkOrange;
            padding: 10px; 
            }
          table.roundedCorners tr:last-child > td {
            border-bottom: none;
            }
        </style>
        ';

        $html='
        <table class="roundedCorners" >
        <tr><th colspan="4">'.$list['title'].'</th></tr>
        ';
        foreach($list['winners'] as $i=>$row){
            $html.='
                <tr><td>'.$row['place'].'</td><td>'.$row['name'].'</td><td>'.$row['abbr'].'</td><td></td></tr>
            ';
        }
        $html.='
        </table>
        
        '.$htmlStyle;
        // echo $html;
        // die();
        $this->pdf->writeHTMLCell(80, '', $x, $y, $html, 0, 1, 0, true, 'J', true);

    }



}