<?php
namespace App\Services\Printer;
use App\Helpers\PdfHelper;

use TCPDF;

class WinnerService{
    
    protected $pdf=null;
    protected $gameSetting=null;
    protected $title='Judo Competition of Asia Pacific';
    protected $title_sub='Judo Union of Asia';
    protected $logo_primary='images/jua_logo.png';
    protected $logo_secondary=null;
    protected $gender='Men';
    protected $report_title='Final Results';

    protected $startX=25; //面頁基點X軸
    protected $startY=30; //面頁基點Y軸
    protected $boxW=45; //運動員名牌高度
    protected $boxH=14; //運動員名牌寛度
    protected $boxGap=4; //運動員名牌之間距離
    protected $arcW=20; //上線曲線寛度
    protected $arcWFirst=4; //第一輪上線曲線
    protected $repechageDistance=5; //復活賽表,運動員名牌之間距離
    protected $repechageBoxGap=2; //復活賽表,運動員名牌之間距離
    protected $repechageSectionGap=5; //復活賽表, second
    protected $circleSize=3;
    protected $playerFontSize=10;

    protected $repechageMode='QUARTER'; //QUARTER, DOUBLE, FULL 
    protected $round=0;
    protected $playerCount=0;
    
    protected $titleFont='times';
    protected $playerFont='times';
    protected $generalFont='times';
    
    protected $arcColor=array(50, 50, 127);
    protected $boxWhiteColor=array(240,240,255);
    protected $boxBlueColor=array(255,255,255);
    protected $circleColor=array(240,240,240);
    protected $styleWinnerLine=null;
    protected $styleArcLine=null;
    protected $styleBoxLine=null;
    protected $styleResult1=null;
    protected $styleResult2=null;
    protected $styleCircle=null;
    protected $resultColor1=array(200,200,200);
    protected $resultColor2=array(250,250,250);
    
    protected $genderColor=[
        'men'=>['header'=>array(168,189,208),'body'=>array(198,214,227)],
        'women'=>['header'=>array(255,182,102),'body'=>array(255,207,155)]
    ];


    public function __construct($settings){
        $this->gameSetting=$settings;
        $this->styleWinnerLine = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(255, 0, 0));
        $this->styleArcLine = array('width' => 0.10, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50, 127));
        $this->styleBoxLine= array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(127, 127, 127));
        $this->styleResult1 = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleResult2 = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'color' => array(0, 0, 0));
        $this->styleCircle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(50, 50,127));

    }
    public function setLogos($primary=null, $secondary=null){
        $this->logo_primary=$primary;
        $this->logo_secondary=$secondary;
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

    public function pdf($gender='Man', $winnerList=[]){

        $this->gender=ucfirst($gender);
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
        $helper=new PdfHelper($this->pdf);
        $extra=["title"=>$this->gender,"title_sub"=>$this->report_title];
        $helper->header2(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary,$extra);
        $this->printResult($winnerList);
        $this->pdf->Output('myfile.pdf', 'I');
    }
    public function printResult($winnerList){
        //$this->pdf->setXY($this->startX, $this->startY);
        $this->pdf->setFont('times','B',10);
        $x=$this->startX;
        $y=$this->startY;
        $leftX=20;
        $rightX=110;
        for($i=0;$i<count($winnerList);$i=$i+2){
            $this->printTable($leftX, $y, $winnerList[$i]);
            if(isset($winnerList[$i+1])){
                $this->printTable($rightX, $y, $winnerList[$i+1]);
            }
            $y+=60;
        }
        //$this->pdf->writeHTML($html, true, false, false, false, '');

    }
    public function printTable($x, $y, $list){
        $htmlStyle='
        <style>
        table.tbl { 
            border: none;
            border-radius: 13px; 
            border-spacing: 0;
            border-collapse: collapse;
            }
          table.tbl td { 
            border: 1px solid Dark;
            padding: 10px; 
            background-color:rgb('.implode(',',$this->genderColor[strtolower($this->gender)]['body']).');
            font-size:10px;
            border-collapse: collapse;
          }
         img{
            height: 10px;
            float: right;
         }
        </style>
        ';

        $html='
        <table class="tbl" cellspacing="0" cellpadding="1" border="0">
        <tr><th width="20"></th><th width="150"></th><th width="50"></th></tr>
        ';
        foreach($list['winners'] as $i=>$row){
            $html.='
                <tr><td style="text-align:center;">'.$row['place'].'</td><td>'.$row['name'].'</td><td>'
            ;
            $html.='<table width="100%" style="border:none"><tr><td width="60%">'.$row['abbr'].'</td><td width="40%" style="text-align:right"><img src="'.$row['logo'].'"></td></tr></table>';
            $html.='</td></tr>';
        }
        $html.='
        <tr><th colspan="3"></th></tr>
        </table>
        
        '.$htmlStyle;
        // echo $html;
        // die();

        $this->pdf->writeHTMLCell(80, '', $x, $y, $html, 0, 1, 0, true, 'J', true);
        $x+=1;
        $y+=1;
        $w=77.7;
        $h=5;
        $r=2;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '1001', 'DF', $this->styleBoxLine, $this->genderColor[strtolower($this->gender)]['header']);
        $this->pdf->setXY($x, $y);
        $this->pdf->Cell($w, $h, $list['title'], 0, 1, 'C', 0, '', 0);
        $y+=45.5;
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, '0110', 'DF', $this->styleBoxLine, $this->genderColor[strtolower($this->gender)]['header']);

    }



}