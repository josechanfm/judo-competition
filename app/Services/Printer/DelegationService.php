<?php
namespace App\Services\Printer;

use App\Helpers\PdfHelper;
use TCPDF;

class DelegationService{
    
    protected $pdf=null;
    protected $gameSetting=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';
    protected $logo_primary='images/jua_logo.png';
    protected $logo_secondary=null;
    protected $weight=null;
    protected $category=null;

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

    public function pdf($records, $weight=null, $category=null){
        $this->weight=$weight;
        $this->category=$category;

        $this->pdf = new \Mpdf\Mpdf();
        //$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // $this->pdf->SetPrintHeader(false);
        // $this->pdf->SetMargins(15,10,15);
        // $this->pdf->SetAutoPageBreak(TRUE,0);
        $this->pdf->AddPage();
        //$this->header();
        $helper=new PdfHelper($this->pdf);
        $extra=["title"=>$this->weight,"title_sub"=>$this->category];
        $helper->header2(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary,$extra);
        $this->schedule($records);
        $this->pdf->Output('myfile.pdf', 'I');
    }

    public function schedule($records){
        $this->pdf->setXY($this->startX, $this->startY);
        $this->pdf->setFont('times','B',11);
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
                    border: 1px solid #000; 
                    border-radius: 10px;
                    font-family:Serif;
                    font-size:10;
                }
                .tableMain td{
                    height:22px;
                    text-align: center;
                    padding: 0px;
                    border-collapse: collapse;
                    vertical-align: middle;
                    border: 1px solid #000; 
                    border-radius: 10px;
                }
                </style>
            </head>
        ';
        $data.='<table class="tableMain"><tr>';
        $data.='<tr>';
        $data.='<td rowspan="2" style="font-size:10px;width:30px">#</td>';
        $data.='<td rowspan="2" colspan="2" style="width:100px">Nation</td>';
        $data.='<td rowspan="2" style="width:120px;font-size:12px">FAMILY NAME</td>';
        $data.='<td rowspan="2" style="width:120px;;font-size:12px">Given Name</td>';
        $data.='<td rowspan="2" style="width:70px; font-size:10px">Date of Birth</td>';
        $data.='<td style="font-size:10px">Exact Weight</td>';
        $data.='<td style="font-size:10px">Signature of player</td>';
        $data.='<td rowspan="2" style="width:20px;font-size:12px">Out</td>';
        $data.='</tr>';
        $data.='<tr>';
        $data.='<td style="font-size:8px">(one position after decimal point)</td>';
        $data.='<td style="font-size:8px">(only in case of overweight)</td>';
        $data.='</tr>';
        foreach($records as $i=>$record){
            $data .='<tr>';
            $data .='<td>'.($i+1).'</td>';
            $data .='<td><img src="'.$record["logo"].'" width="30px"/></td>';
            $data .='<td>'.$record["team"].'</td>';
            $data .='<td>'.$record["family_name"].'</td>';
            $data .='<td>'.$record["given_name"].'</td>';
            $data .='<td>'.$record["dob"].'</td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='<td></td>';
            $data .='</tr>';
        }
        $data .='</table>';
        $this->pdf->WriteHTML($data);
        // $x=100;
        // $y=100;
        // $this->pdf->writeHTMLCell(80, '', $x, $y, $data, 0, 1, 0, true, 'J', true);
        $this->pdf->Output('myfile.pdf','I');
    }

}
