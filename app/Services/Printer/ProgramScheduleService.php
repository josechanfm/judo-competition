<?php

namespace App\Services\Printer;

use App\Helpers\PdfHelper;
use TCPDF;

class ProgramScheduleService
{

    protected $pdf = null;
    protected $gameSetting = null;
    protected $title = '2025全澳柔道錦標賽';
    protected $title_sub = '';
    protected $logo_primary = 'images/mja_logo.png';
    protected $logo_secondary = null;

    protected $startX = 15; //面頁基點X軸
    protected $startY = 30; //面頁基點Y軸
    protected $boxWhiteColor = array(240, 240, 255);
    protected $boxBlueColor = array(255, 255, 255);
    protected $styleBoxLine = null;
    protected $recordsPerPage = 10; // 每頁最多顯示1行數據


    public function __construct()
    {
        $this->styleBoxLine = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
        // $this->configureFonts();
    }

    protected function configureFonts()
    {
        // 定義字型配置
        $fontConfig = [
            'defaultFont' => 'NotoSerifTC', // 設置預設字型
            'fontdata' => [
                'R' => 'NotoSerifTC-Regular.ttf',
                'B' => 'NotoSerifTC-Bold.ttf',
            ]
        ];

        // 創建 mPDF 實例時傳入字型配置
        $this->pdf = new \Mpdf\Mpdf($fontConfig);
    }

    protected function createPdfInstance()
    {
        // 正確的 mPDF 配置格式
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 12,
            'default_font' => 'NotoSerifTC',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 25,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'tempDir' => storage_path('app/tmp'), // 確保有暫存目錄
            'fontDir' => [
                resource_path('fonts'), // 指定字型目錄
                base_path('resources/fonts'),
            ],
            'fontdata' => [
                'notoseriftc' => [
                    'R' => 'NotoSerifTC-Regular.ttf',
                    'B' => 'NotoSerifTC-Bold.ttf',
                ]
            ],
        ];

        return new \Mpdf\Mpdf($config);
    }

    public function setLogos($primary = null, $secondary = null)
    {
        $this->logo_primary = $primary;
        $this->logo_secondary = $secondary;
    }

    public function setTitles($title = null, $title_sub = null)
    {
        $this->title = $title;
        $this->title_sub = $title_sub;
    }

    public function pdf($records, $mat = null, $date = null)
    {
        $this->pdf = $this->createPdfInstance();
        $this->pdf->AddPage();

        $helper = new PdfHelper($this->pdf);
        $extra = ["title" => $mat, "title_sub" => $date];
        $helper->header2(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $extra);
        
        $this->schedule($records);
        $this->pdf->Output('myfile.pdf', 'I');
    }

    // 修改後的方法：處理所有賽程的分頁顯示
    public function allSchedulesPdf($allBouts)
    {
        $this->pdf = $this->createPdfInstance();
        // dd($this->title_sub);
        foreach ($allBouts as $schedule) {
            $mat = $schedule['mat'];
            $section = $schedule['section'];
            $date = $schedule['date'];
            $bouts = $schedule['bouts'];
            
            // 將比賽數據分頁
            $chunks = array_chunk($bouts->toArray(), $this->recordsPerPage);
            $pageCount = count($chunks);
            
            for ($page = 0; $page < $pageCount; $page++) {
                $this->pdf->AddPage();
                
                $helper = new PdfHelper($this->pdf);
                $ellipseData = [
                    "title" => "場地{$mat}-時段{$section}", 
                    "title_sub" => $date ? $date : "",
                ];
                
                $helper->header2(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, 'mingliu', $ellipseData);
                
                // 生成該頁的賽程表 - 使用新的方法避免輸出
                $this->renderSchedulePage(collect($chunks[$page]));
            }
        }
        
        // 只在最後輸出一次
        $this->pdf->Output('all_schedules.pdf', 'I');
    }

    // 新增方法：只渲染賽程頁面，不輸出
    public function renderSchedulePage($records)
    {
        $this->pdf->setXY($this->startX, $this->startY);
        $data = $this->generateScheduleHtml($records);
        $this->pdf->WriteHTML($data);
        // 注意：這裡移除了 Output 方法
    }

    // 新增方法：生成賽程HTML
// 在 generateScheduleHtml 方法中修改字體大小
protected function generateScheduleHtml($records)
{
    // dd($records);
    $data = '
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
                margin-bottom: 10px;
            }
            .tableMain td{
                text-align: center;
                padding: 0px;
                border-collapse: collapse;
                vertical-align: middle;
                font-size: 14px; /* 增加基礎字體大小 */
            }
            .cancelled-bout {
                width:100%;
                display: inline-block;
                border-collapse: collapse;
                margin-bottom: 10px;
                background-color: #c0c0c0;
            }
            .cancelled-bout td{
                text-align: center;
                padding: 0px;
                border-collapse: collapse;
                vertical-align: middle;
                font-size: 14px; /* 增加基礎字體大小 */
            }
            .tableRight{
                width: 300px;
                border-collapse: collapse;
            }
            .tableRight td  {
                height:24.5px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000; 
            }
            .tableLeft {
                float:left;
                width: 550px;
                border-collapse: collapse;
            }
            .tableLeft td  {
                height:28px;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #000; 
            }
            .arc{
                position: absolute;
                display: inline-block;
                height: 72.5px;
                width: 40px;
                border-radius: 100% / 100% 0% 0% 100%;
                border: 1px solid #000;
                color: #fff;
                text-align: center;
                font-size: 14px; /* 增加弧形區域字體大小 */
            }
            .spacer{
                height:10px;
            }
            .page-info {
                text-align: center;
                font-size: 12px; /* 增加頁面信息字體大小 */
                margin: 5px 0;
                color: #666;
            }
            /* 新增灰色背景樣式 */

            </style>
        </head>
        <body>
    ';
    
    $rightTable = '
    <div style="flow:right">
    <table class="tableRight">
        <tr>
            <td style="font-size:14px;"></td> <!-- 增加字體大小 -->
            <td style="font-size:14px;"></td>
            <td style="font-size:14px;"></td>
        </tr>
        <tr>
            <td style="font-size:14px;"></td>
            <td style="font-size:14px;"></td>
            <td style="font-size:14px;"></td>
        </tr>
        <tr>
            <td style="font-size:14px;"></td>
            <td style="font-size:14px;"></td>
            <td style="font-size:14px;"></td>
        </tr>
    </table>
    </div>
    ';
    
    foreach ($records as $record) {
        // 檢查 status 是否為 -1，決定是否添加灰色背景
        $containerClass = ($record['status'] == -1) ? 'cancelled-bout' : 'tableMain';

        $data .= '<table class="'. $containerClass . '"><tr>';
        $data .= '<td>';
        $data .= '<table class="tableLeft">';
        $data .= '<tr>';
        $data .= '<td rowspan="4" style="width:42px;border:none!important;font-size:18px;"><div class="arc"></div>' . ($record['sequence'] ?? '') . '</td>'; // 增加序列號字體
        $data .= '<td rowspan="4" style="width:80px;font-size:16px;">' . ($record['weight'] ?? '') . '<br><font style="font-size:12px;font-weight:bold">' . mb_substr($record['category'] ?? '', 0, 10) . '</font><br><font style="font-size:11px;font-weight:bold">' . ($record['round'] ?? '') . '</font></td>'; // 增加重量和類別字體
        if($record['white_is_weight_passed'] == 0){
            $data .= '<td rowspan="2" style="width:190px; font-size:15px;"><del>' . mb_substr($record['white_player'] ?? '', 0, 18) . '</del></td>'; // 增加白方選手字體
        }else {
            $data .= '<td rowspan="2" style="width:190px; font-size:15px;">' . mb_substr($record['white_player'] ?? '', 0, 18) . '</td>'; // 增加白方選手字體
        }
        $data .= '<td rowspan="2" style="width:140px; font-size:14px;">' . ($record['white_team'] ?? '') . '</td>'; // 增加白方隊伍字體
        $data .= '<td style="font-size:14px;"></td>'; // 增加其他單元格字體
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '</tr>';
        $data .= '<tr>';
        $data .= '<td rowspan="2" style="font-size:13px;width:15px;">I</td>'; // 增加標識字體
        $data .= '<td rowspan="2" style="font-size:13px;width:15px">W</td>';
        $data .= '<td rowspan="2" style="font-size:13px;width:15px">Y</td>';
        $data .= '<td rowspan="2" style="font-size:13px;width:15px">S</td>';
        $data .= '<td rowspan="2" style="font-size:13px;">' . ($record['time'] ?? '') . '</td>'; // 增加時間字體
        $data .= '</tr>';
        $data .= '<tr>';
        if($record['blue_is_weight_passed'] == 0){
            $data .= '<td rowspan="2" style="background:lightblue; font-size:14px;"><del>' . mb_substr($record['blue_player'] ?? '', 0, 18). '</del></td>'; // 增加藍方選手字體
        }else {
            $data .= '<td rowspan="2" style="background:lightblue; font-size:14px;">' . mb_substr($record['blue_player'] ?? '', 0, 18). '</td>';
        }
        $data .= '<td rowspan="2" style="background:lightblue; font-size:14px;">' . ($record['blue_team'] ?? '') . '</td>'; // 增加藍方隊伍字體
        $data .= '</tr>';
        $data .= '<tr>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '<td style="font-size:14px;"></td>';
        $data .= '</tr>';
        $data .= '</table>';
        $data .= '</td>';
        $data .= '<td style="width:4px"></td>';
        $data .= '<td>' . $rightTable . '</td>';
        $data .= '</tr></table>';
        $data .= '<div class="spacer"></div>';
    }
    
    $data .= '</body></html>';
    
    return $data;
}

    // 保持原有的 schedule 方法兼容性（但移除其中的 Output）
    public function schedule($records)
    {
        $this->pdf->setXY($this->startX, $this->startY);
        $data = $this->generateScheduleHtml($records);
        $this->pdf->WriteHTML($data);
        // 注意：移除了這裡的 Output 方法
    }

    // 新增方法：設置每頁顯示的行數
    public function setRecordsPerPage($number)
    {
        $this->recordsPerPage = max(1, $number); // 至少顯示1行
        return $this;
    }

    // 新增方法：獲取當前每頁顯示行數
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }
    
}