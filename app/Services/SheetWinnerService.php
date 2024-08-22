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
        $this->pdf->setXY($this->startX, $this->startY);
        $html ='
        <table cellspacing="0" cellpadding="1" border="0">
            <tr><td>1</td><td></td><td></td><td></td></tr>
            <tr><td>2</td><td></td><td></td><td></td></tr>
            <tr><td>3</td><td></td><td></td><td></td></tr>
            <tr><td>4</td><td></td><td></td><td></td></tr>
        </table>
        ';


        $html.='
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
            .num{
                background-color:rgb(254,206,50);
                width:22px;
                text-align:center;
            }
            .num2{
                background-color:rgb(245,158,51);
                width:20px;
                text-align:center;
            }
        </style>
        ';
        $this->pdf->writeHTML($html, true, false, false, false, '');

    }



}