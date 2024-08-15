<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function htmlToPdf(){
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
        // $data .='<div>';
        // $data .='<div class="arc"></div>';
        // $data .='<span class="arc">B</span>';
        // $data .='</div>';
        //echo $data;
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($data);
        $mpdf->Output('myfile.pdf', 'I');
    }

    public function programs(){
        $file=storage_path('template//CK_B4_StudentRegistrationForm.xlsx');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($file);
        $data=[
            "B1"=>"2024",
            "B2"=>"初一",
            "B3"=>"A",
            "B4"=>"13",
            "B5"=>"陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民 陳輝民",
            "B6"=>"Jose Chan",
            "B7"=>"M",
            "B8"=>"1970",
            "C8"=>"07",
            "D8"=>"18",
            "B9"=>"中國",
            "B22"=>"陳大文",
            "C22"=>"父親",
            "D22"=>"54",
            "E22"=>"programmer",
            "F22"=>"my address ....",
            "G22"=>"63860836",
            "B23"=>"",
            "C23"=>"",
            "D23"=>"",
            "E23"=>"",
            "F23"=>"",
            "G23"=>"",
            "B28"=>"YES",
            "B32"=>"2024-03-20",
        ];
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(1);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.4);

        $spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:K27');
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(1);
        
        

        //$spreadsheet->getActiveSheet()->getPageMargins()->setFooter(0.5);
        //$spreadsheet->getActiveSheet()->getHeaderFooter()->setFirstFooter('First footer');
        // $spreadsheet->getActiveSheet()->getHeaderFooter()->setEvenFooter('abc');
        // $spreadsheet->getActiveSheet()->getHeaderFooter()->setOddFooter('abc');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        $drawing->setPath(storage_path('template//P1A01.png')); /* put your path and image here */
        $drawing->setCoordinates('K4');
        $drawing->setWidth(150);
        $drawing->setHeight(150);
        //$drawing->setOffsetX(110);
        //$drawing->setRotation(25);
        //$drawing->getShadow()->setVisible(true);
        //$drawing->getShadow()->setDirection(45);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $spreadsheet->getActiveSheet()->getCell('k4')->setValue('');
    
        $sheet1=$spreadsheet->getSheet(1);
        foreach($data as $key=>$value){
            $sheet1->getCell($key)->setValue($value);
        }
        
       $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
       $pdfPath=storage_path('template//form1.pdf');
       $writer->save($pdfPath);

        // Preview the PDF
        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="form1.pdf"');
            header('Content-Length: ' . filesize($pdfPath));
            readfile($pdfPath);
            exit;
        } else {
            echo "File not found.";
        }

    }
}
