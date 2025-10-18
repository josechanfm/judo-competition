<?php

namespace App\Helpers;

class PdfHelper
{

    protected $pdf = null;
    protected $boxLine = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(0, 0, 0));
    protected $boxColor = array(240, 240, 255);
    protected $boxColor2 = array(197, 192, 139);

    public function __construct($pdf)
    {
        $this->pdf = $pdf;
    }

    public function header1($x = 0, $y = 0, $title = null, $title_sub = null, $logo_primary = null, $logo_secondary = null, $titleFont = 'times', $ellipseData = null)
    {
        $w = 190;
        $h = 18;
        $r = 3;
        
        // 計算置中的 x 座標 (假設頁面寬度為 210mm A4)
        $pageWidth = 210;
        $centeredX = ($pageWidth - $w) / 2;
        
        // 先繪製圓角邊框作為 clipping path
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            // 對於 Mpdf，使用 Save/Restore 來限制繪圖區域
            $this->pdf->StartTransform();
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'CNZ'); // CNZ 創建 clipping path
        } else {
            // 對於 TCPDF，使用 clipping
            $this->pdf->StartTransform();
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'CNZ');
        }
        
        // 創建更自然的漸變背景（現在會被限制在圓角內）
        $gradientWidth = $w;
        $gradientHeight = $h;
        $steps = 50; // 大幅增加漸變步數讓過渡更平滑
        
        // 定義漸變顏色 - 使用更和諧的銀白色調
        $silver = [220, 220, 220];  // 較亮的銀色
        $white = [255, 255, 255];   // 純白
        $lightSilver = [240, 240, 240]; // 過渡用的亮銀色
        
        // 繪製漸變背景（現在會被限制在圓角內）
        for ($i = 0; $i < $steps; $i++) {
            $stepX = $centeredX + ($gradientWidth / $steps) * $i;
            $stepWidth = $gradientWidth / $steps;
            
            // 計算漸變顏色 - 使用更平滑的過渡
            if ($i < $steps * 0.3) {
                // 左側 30%: 銀 → 亮銀
                $ratio = $i / ($steps * 0.3);
                $rColor = $silver[0] + ($lightSilver[0] - $silver[0]) * $ratio;
                $gColor = $silver[1] + ($lightSilver[1] - $silver[1]) * $ratio;
                $bColor = $silver[2] + ($lightSilver[2] - $silver[2]) * $ratio;
            } elseif ($i < $steps * 0.7) {
                // 中間 40%: 亮銀 → 白 → 亮銀
                $midRatio = ($i - $steps * 0.3) / ($steps * 0.4);
                if ($midRatio < 0.5) {
                    // 前半: 亮銀 → 白
                    $ratio = $midRatio * 2;
                    $rColor = $lightSilver[0] + ($white[0] - $lightSilver[0]) * $ratio;
                    $gColor = $lightSilver[1] + ($white[1] - $lightSilver[1]) * $ratio;
                    $bColor = $lightSilver[2] + ($white[2] - $lightSilver[2]) * $ratio;
                } else {
                    // 後半: 白 → 亮銀
                    $ratio = ($midRatio - 0.5) * 2;
                    $rColor = $white[0] - ($white[0] - $lightSilver[0]) * $ratio;
                    $gColor = $white[1] - ($white[1] - $lightSilver[1]) * $ratio;
                    $bColor = $white[2] - ($white[2] - $lightSilver[2]) * $ratio;
                }
            } else {
                // 右側 30%: 亮銀 → 銀
                $ratio = ($i - $steps * 0.7) / ($steps * 0.3);
                $rColor = $lightSilver[0] - ($lightSilver[0] - $silver[0]) * $ratio;
                $gColor = $lightSilver[1] - ($lightSilver[1] - $silver[1]) * $ratio;
                $bColor = $lightSilver[2] - ($lightSilver[2] - $silver[2]) * $ratio;
            }
            
            // 繪製漸變矩形（現在會被限制在圓角內）
            if (get_class($this->pdf) == 'Mpdf\Mpdf') {
                $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                $this->pdf->Rect($stepX, $y, $stepWidth, $gradientHeight, 'F');
            } else {
                $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                $this->pdf->Rect($stepX, $y, $stepWidth, $gradientHeight, 'F');
            }
        }
        
        // 結束 clipping
        $this->pdf->StopTransform();
        
        // 重新繪製邊框（在背景之上）
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetLineWidth(0.2);
            $this->pdf->SetDrawColor(180, 180, 180);
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'D');
        } else {
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'D', $this->boxLine, [180, 180, 180]);
        }
        
        // 圖片放在左邊
        $leftMargin = 5;
        $logoSpacing = 2; // 圖標之間的間距

        if ($logo_primary) {
            $this->pdf->image($logo_primary, $centeredX + $leftMargin, $y + 2, 14, 14, 'png');
        }
        if ($logo_secondary) {
            $primaryWidth = $logo_primary ? 14 + $logoSpacing : 0;
            $this->pdf->image($logo_secondary, $centeredX + $leftMargin + $primaryWidth, $y + 2, 14, 14, 'png');
        }
        
        // 先計算右側內容的寬度，以便確定分隔線位置
        $rightContentWidth = 0;
        if ($ellipseData !== null) {
            $program = $ellipseData["program"];
            $weight = $ellipseData["weight"];
            $athletes_count = isset($ellipseData["athletes_count"]) ? $ellipseData["athletes_count"] : "";
            
            $programText = $program;
            if (!empty($athletes_count)) {
                $programText .= " (" . $athletes_count . ")";
            }
            
            // 計算文字寬度
            $this->pdf->setFont($titleFont, 'B', 20); // weight 字型
            $weightWidth = $this->pdf->GetStringWidth($weight);
            
            $this->pdf->setFont($titleFont, 'B', 11); // program 字型
            $programWidth = $this->pdf->GetStringWidth($programText);
            
            $rightContentWidth = max($weightWidth, $programWidth) + 15; // 加上額外間距
        }
        
        // 繪製分隔線 - 在標題和右側內容之間
        $lineMargin = 10; // 分隔線距離右側內容的間距
        $lineX = $centeredX + $w - $rightContentWidth - $lineMargin;
        $lineTopMargin = 3; // 上下邊距
        $lineY1 = $y + $lineTopMargin;
        $lineY2 = $y + $h - $lineTopMargin;
        
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetDrawColor(150, 150, 150); // 灰色分隔線
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($lineX, $lineY1, $lineX, $lineY2);
        } else {
            $this->pdf->SetDrawColor(150, 150, 150); // 灰色分隔線
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($lineX, $lineY1, $lineX, $lineY2);
        }
        
        // 標題放在中間（但限制寬度避免與右側內容重疊）
        $titleWidth = $lineX - $centeredX - 10; // 標題寬度為分隔線左側減去間距
        
        if($title_sub == null){
            $this->pdf->setFont($titleFont, 'B', 24);
            $this->pdf->setXY($centeredX, $y);
            $this->pdf->Cell($titleWidth, $h / 1, $title, 0, 1, 'C', 0, '', 0);
        } else {
            $this->pdf->setFont($titleFont, 'B', 20);
            $this->pdf->setXY($centeredX, $y);
            $this->pdf->Cell($titleWidth, $h / 1.6, $title, 0, 1, 'C', 0, '', 0);
            $this->pdf->setFont($titleFont, 'B', 13);
            $this->pdf->setXY($centeredX, $y + ($h / 1.6));
            $this->pdf->Cell($titleWidth, $h - ($h / 1.6), $title_sub, 0, 0, 'C', 0, '', 0);
        }
        
        // 其他內容（橢圓數據）放在右邊並垂直置中
        if ($ellipseData !== null) {
            $this->pdf->SetTextColor(0, 0, 0);
            
            $program = $ellipseData["program"];
            $weight = $ellipseData["weight"];
            $athletes_count = isset($ellipseData["athletes_count"]) ? $ellipseData["athletes_count"] : "";
            
            $rightMargin = 10;
            
            // 組合 program 和 athletes_count
            $programText = $program;
            if (!empty($athletes_count)) {
                $programText .= " (" . $athletes_count . ")";
            }
            
            // 計算文字寬度
            $this->pdf->setFont($titleFont, 'B', 20); // weight 字型
            $weightWidth = $this->pdf->GetStringWidth($weight);
            
            $this->pdf->setFont($titleFont, 'B', 11); // program 字型
            $programWidth = $this->pdf->GetStringWidth($programText);
            
            $maxWidth = max($weightWidth, $programWidth);
            
            // 右側起始位置
            $rightStartX = $lineX + $lineMargin;
            
            // 計算垂直置中的 Y 位置
            $totalTextHeight = 16; // 兩行文字的總高度
            $startY = $y + ($h - $totalTextHeight) / 2;
            
            // 繪製右側內容（weight 在上面，program 在下面）
            $this->pdf->setFont($titleFont, 'B', 20); // weight 字型
            $this->pdf->SetXY($rightStartX, $startY);
            $this->pdf->Cell($maxWidth, 8, $weight, 0, 1, 'C'); // 水平置中
            
            $this->pdf->setFont($titleFont, 'B', 11); // program 字型
            $this->pdf->SetXY($rightStartX, $startY + 8);
            $this->pdf->Cell($maxWidth, 8, $programText, 0, 0, 'C'); // 水平置中
        }
    }
    public function header2($x = 0, $y = 0, $title = null, $title_sub = null, $logo_primary = null, $logo_secondary = null, $titleFont = 'times', $ellipseData = null)
    {
        $w = 190;
        $h = 18;
        $r = 3;
        
        // 計算置中的 x 座標 (假設頁面寬度為 210mm A4)
        $pageWidth = 210;
        $centeredX = ($pageWidth - $w) / 2;
        
        // 對於 mPDF，使用不同的方法來限制繪圖區域
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            // mPDF 的方法：先繪製圓角矩形背景，再繪製邊框
            
            // 創建更自然的漸變背景 - 直接繪製在整個圓角矩形區域
            $steps = 50;
            $silver = [220, 220, 220];
            $white = [255, 255, 255];
            $lightSilver = [240, 240, 240];
            
            // 繪製漸變背景 - 使用整個區域，不留邊距
            for ($i = 0; $i < $steps; $i++) {
                $stepX = $centeredX + ($w / $steps) * $i;
                $stepWidth = $w / $steps;
                
                // 計算漸變顏色
                if ($i < $steps * 0.3) {
                    $ratio = $i / ($steps * 0.3);
                    $rColor = $silver[0] + ($lightSilver[0] - $silver[0]) * $ratio;
                    $gColor = $silver[1] + ($lightSilver[1] - $silver[1]) * $ratio;
                    $bColor = $silver[2] + ($lightSilver[2] - $silver[2]) * $ratio;
                } elseif ($i < $steps * 0.7) {
                    $midRatio = ($i - $steps * 0.3) / ($steps * 0.4);
                    if ($midRatio < 0.5) {
                        $ratio = $midRatio * 2;
                        $rColor = $lightSilver[0] + ($white[0] - $lightSilver[0]) * $ratio;
                        $gColor = $lightSilver[1] + ($white[1] - $lightSilver[1]) * $ratio;
                        $bColor = $lightSilver[2] + ($white[2] - $lightSilver[2]) * $ratio;
                    } else {
                        $ratio = ($midRatio - 0.5) * 2;
                        $rColor = $white[0] - ($white[0] - $lightSilver[0]) * $ratio;
                        $gColor = $white[1] - ($white[1] - $lightSilver[1]) * $ratio;
                        $bColor = $white[2] - ($white[2] - $lightSilver[2]) * $ratio;
                    }
                } else {
                    $ratio = ($i - $steps * 0.7) / ($steps * 0.3);
                    $rColor = $lightSilver[0] - ($lightSilver[0] - $silver[0]) * $ratio;
                    $gColor = $lightSilver[1] - ($lightSilver[1] - $silver[1]) * $ratio;
                    $bColor = $lightSilver[2] - ($lightSilver[2] - $silver[2]) * $ratio;
                }
                
                // 繪製漸變矩形 - 使用整個高度和寬度
                $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                $this->pdf->Rect($stepX, $y, $stepWidth, $h, 'F');
            }
            
            // 最後繪製圓角邊框
            $this->pdf->SetLineWidth(0.2);
            $this->pdf->SetDrawColor(180, 180, 180);
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'D');
            
        } else {
            // TCPDF 的原有邏輯
            $this->pdf->StartTransform();
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'CNZ');
            
            // 漸變背景邏輯...
            $steps = 50;
            $silver = [220, 220, 220];
            $white = [255, 255, 255];
            $lightSilver = [240, 240, 240];
            
            for ($i = 0; $i < $steps; $i++) {
                $stepX = $centeredX + ($w / $steps) * $i;
                $stepWidth = $w / $steps;
                
                if ($i < $steps * 0.3) {
                    $ratio = $i / ($steps * 0.3);
                    $rColor = $silver[0] + ($lightSilver[0] - $silver[0]) * $ratio;
                    $gColor = $silver[1] + ($lightSilver[1] - $silver[1]) * $ratio;
                    $bColor = $silver[2] + ($lightSilver[2] - $silver[2]) * $ratio;
                } elseif ($i < $steps * 0.7) {
                    $midRatio = ($i - $steps * 0.3) / ($steps * 0.4);
                    if ($midRatio < 0.5) {
                        $ratio = $midRatio * 2;
                        $rColor = $lightSilver[0] + ($white[0] - $lightSilver[0]) * $ratio;
                        $gColor = $lightSilver[1] + ($white[1] - $lightSilver[1]) * $ratio;
                        $bColor = $lightSilver[2] + ($white[2] - $lightSilver[2]) * $ratio;
                    } else {
                        $ratio = ($midRatio - 0.5) * 2;
                        $rColor = $white[0] - ($white[0] - $lightSilver[0]) * $ratio;
                        $gColor = $white[1] - ($white[1] - $lightSilver[1]) * $ratio;
                        $bColor = $white[2] - ($white[2] - $lightSilver[2]) * $ratio;
                    }
                } else {
                    $ratio = ($i - $steps * 0.7) / ($steps * 0.3);
                    $rColor = $lightSilver[0] - ($lightSilver[0] - $silver[0]) * $ratio;
                    $gColor = $lightSilver[1] - ($lightSilver[1] - $silver[1]) * $ratio;
                    $bColor = $lightSilver[2] - ($lightSilver[2] - $silver[2]) * $ratio;
                }
                
                $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                $this->pdf->Rect($stepX, $y, $stepWidth, $h, 'F');
            }
            
            $this->pdf->StopTransform();
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'D', $this->boxLine, [180, 180, 180]);
        }
        
        // 圖片放在左邊
        $leftMargin = 5;
        $logoSpacing = 2;

        if ($logo_primary) {
            $this->pdf->image($logo_primary, $centeredX + $leftMargin, $y + 2, 14, 14, 'png');
        }
        if ($logo_secondary) {
            $primaryWidth = $logo_primary ? 14 + $logoSpacing : 0;
            $this->pdf->image($logo_secondary, $centeredX + $leftMargin + $primaryWidth, $y + 2, 14, 14, 'png');
        }
        
        // 先計算右側內容的寬度，以便確定分隔線位置
        $rightContentWidth = 0;
        if ($ellipseData !== null) {
            $ellipseTitle = $ellipseData["title"];
            $ellipseTitleSub = $ellipseData["title_sub"];

            $this->pdf->setFont($titleFont, 'B', 8);
            $weightWidth = $this->pdf->GetStringWidth($ellipseTitle);
            
            $this->pdf->setFont($titleFont, 'B', 8);
            $programWidth = $this->pdf->GetStringWidth($ellipseTitleSub);
            
            $rightContentWidth = max($weightWidth, $programWidth) + 15;
        }
        
        // 繪製分隔線 - 在標題和右側內容之間
        $lineMargin = 10;
        $lineX = $centeredX + $w - $rightContentWidth - $lineMargin;
        $lineTopMargin = 3;
        $lineY1 = $y + $lineTopMargin;
        $lineY2 = $y + $h - $lineTopMargin;
        
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetDrawColor(150, 150, 150);
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($lineX, $lineY1, $lineX, $lineY2);
        } else {
            $this->pdf->SetDrawColor(150, 150, 150);
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($lineX, $lineY1, $lineX, $lineY2);
        }
        
        // 標題放在中間（但限制寬度避免與右側內容重疊）
        $titleWidth = $lineX - $centeredX - 10;
        
        if($title_sub == null){
            $this->pdf->setFont($titleFont, 'B', 24);
            $this->pdf->setXY($centeredX, $y);
            $this->pdf->Cell($titleWidth, $h / 1, $title, 0, 1, 'C', 0, '', 0);
        } else {
            $this->pdf->setFont($titleFont, 'B', 20);
            $this->pdf->setXY($centeredX, $y);
            $this->pdf->Cell($titleWidth, $h / 1.6, $title, 0, 1, 'C', 0, '', 0);
            $this->pdf->setFont($titleFont, 'B', 13);
            $this->pdf->setXY($centeredX, $y + ($h / 1.6));
            $this->pdf->Cell($titleWidth, $h - ($h / 1.6), $title_sub, 0, 0, 'C', 0, '', 0);
        }
        
        // 其他內容（橢圓數據）放在右邊並垂直置中
        if ($ellipseData !== null) {
            $this->pdf->SetTextColor(0, 0, 0);
            
            $ellipseTitle = $ellipseData["title"];
            $ellipseTitleSub = $ellipseData["title_sub"];
        
            // 計算文字寬度
            $this->pdf->setFont($titleFont, 'B', 11);
            $weightWidth = $this->pdf->GetStringWidth($ellipseTitle);
            
            $this->pdf->setFont($titleFont, 'B', 11);
            $programWidth = $this->pdf->GetStringWidth($ellipseTitleSub);
            
            $maxWidth = max($weightWidth, $programWidth);
            
            // 右側起始位置
            $rightStartX = $lineX + $lineMargin;
            
            // 計算垂直置中的 Y 位置
            $totalTextHeight = 16;
            $startY = $y + ($h - $totalTextHeight) / 2;
            
            // 繪製右側內容
            $this->pdf->setFont($titleFont, 'B', 11);
            $this->pdf->SetXY($rightStartX, $startY);
            $this->pdf->Cell($maxWidth, 8, $ellipseTitle, 0, 1, 'C');
            
            $this->pdf->setFont($titleFont, 'B', 11);
            $this->pdf->SetXY($rightStartX, $startY + 8);
            $this->pdf->Cell($maxWidth, 8, $ellipseTitleSub, 0, 0, 'C');
        }
    }
    public function header3($x = 0, $y = 0, $title = null, $title_sub = null, $logo_primary = null, $logo_secondary = null, $titleFont = 'times', $ellipseData = null)
    {
        $w = 270;  // 增加寬度以適應橫版
        $h = 15;   // 稍微降低高度
        $r = 3;
        
        // 計算置中的 x 座標 (假設橫版頁面寬度為 297mm A4 landscape)
        $pageWidth = 297;
        $centeredX = ($pageWidth - $w) / 2;
        
        // 先繪製圓角邊框作為 clipping path
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            // 對於 Mpdf，使用 Save/Restore 來限制繪圖區域
            $this->pdf->StartTransform();
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'CNZ'); // CNZ 創建 clipping path
        } else {
            // 對於 TCPDF，使用 clipping
            $this->pdf->StartTransform();
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'CNZ');
        }
        
        // 創建更自然的漸變背景（現在會被限制在圓角內）
        $gradientWidth = $w;
        $gradientHeight = $h;
        $steps = 50; // 大幅增加漸變步數讓過渡更平滑
        
        // 定義漸變顏色 - 使用更和諧的銀白色調
        $silver = [220, 220, 220];  // 較亮的銀色
        $white = [255, 255, 255];   // 純白
        $lightSilver = [240, 240, 240]; // 過渡用的亮銀色
        
        // 繪製漸變背景（現在會被限制在圓角內）
        for ($i = 0; $i < $steps; $i++) {
            $stepX = $centeredX + ($gradientWidth / $steps) * $i;
            $stepWidth = $gradientWidth / $steps;
            
            // 計算漸變顏色 - 使用更平滑的過渡
            if ($i < $steps * 0.3) {
                // 左側 30%: 銀 → 亮銀
                $ratio = $i / ($steps * 0.3);
                $rColor = $silver[0] + ($lightSilver[0] - $silver[0]) * $ratio;
                $gColor = $silver[1] + ($lightSilver[1] - $silver[1]) * $ratio;
                $bColor = $silver[2] + ($lightSilver[2] - $silver[2]) * $ratio;
            } elseif ($i < $steps * 0.7) {
                // 中間 40%: 亮銀 → 白 → 亮銀
                $midRatio = ($i - $steps * 0.3) / ($steps * 0.4);
                if ($midRatio < 0.5) {
                    // 前半: 亮銀 → 白
                    $ratio = $midRatio * 2;
                    $rColor = $lightSilver[0] + ($white[0] - $lightSilver[0]) * $ratio;
                    $gColor = $lightSilver[1] + ($white[1] - $lightSilver[1]) * $ratio;
                    $bColor = $lightSilver[2] + ($white[2] - $lightSilver[2]) * $ratio;
                } else {
                    // 後半: 白 → 亮銀
                    $ratio = ($midRatio - 0.5) * 2;
                    $rColor = $white[0] - ($white[0] - $lightSilver[0]) * $ratio;
                    $gColor = $white[1] - ($white[1] - $lightSilver[1]) * $ratio;
                    $bColor = $white[2] - ($white[2] - $lightSilver[2]) * $ratio;
                }
            } else {
                // 右側 30%: 亮銀 → 銀
                $ratio = ($i - $steps * 0.7) / ($steps * 0.3);
                $rColor = $lightSilver[0] - ($lightSilver[0] - $silver[0]) * $ratio;
                $gColor = $lightSilver[1] - ($lightSilver[1] - $silver[1]) * $ratio;
                $bColor = $lightSilver[2] - ($lightSilver[2] - $silver[2]) * $ratio;
            }
            
            // 繪製漸變矩形（現在會被限制在圓角內）
            if (get_class($this->pdf) == 'Mpdf\Mpdf') {
                $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                $this->pdf->Rect($stepX, $y, $stepWidth, $gradientHeight, 'F');
            } else {
                $this->pdf->SetFillColor($rColor, $gColor, $bColor);
                $this->pdf->Rect($stepX, $y, $stepWidth, $gradientHeight, 'F');
            }
        }
        
        // 結束 clipping
        $this->pdf->StopTransform();
        
        // 重新繪製邊框（在背景之上）
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetLineWidth(0.2);
            $this->pdf->SetDrawColor(180, 180, 180);
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'D');
        } else {
            $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'D', $this->boxLine, [180, 180, 180]);
        }
        
        // 圖片放在左邊
        $leftMargin = 8;
        $logoSpacing = 3; // 圖標之間的間距

        if ($logo_primary) {
            $this->pdf->image($logo_primary, $centeredX + $leftMargin, $y + 1.5, 12, 12, 'png');
        }
        if ($logo_secondary) {
            $primaryWidth = $logo_primary ? 12 + $logoSpacing : 0;
            $this->pdf->image($logo_secondary, $centeredX + $leftMargin + $primaryWidth, $y + 1.5, 12, 12, 'png');
        }
        
        // 計算左側內容的寬度（圖片區域）
        $leftContentWidth = 0;
        if ($logo_primary) $leftContentWidth += 12;
        if ($logo_secondary) $leftContentWidth += 12 + $logoSpacing;
        if ($logo_primary || $logo_secondary) $leftContentWidth += $leftMargin;
        
        // 固定右側內容的寬度
        $rightContentWidth = 40; // 固定寬度 40mm
        
        // 繪製分隔線 - 在左側內容和標題之間
        $leftLineMargin = 5;
        $leftLineX = $centeredX + $leftContentWidth + $leftLineMargin;
        $lineTopMargin = 2.5;
        $lineY1 = $y + $lineTopMargin;
        $lineY2 = $y + $h - $lineTopMargin;
        
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetDrawColor(150, 150, 150); // 灰色分隔線
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($leftLineX, $lineY1, $leftLineX, $lineY2);
        } else {
            $this->pdf->SetDrawColor(150, 150, 150); // 灰色分隔線
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($leftLineX, $lineY1, $leftLineX, $lineY2);
        }
        
        // 繪製分隔線 - 在標題和右側內容之間
        $rightLineMargin = 5;
        $rightLineX = $centeredX + $w - $rightContentWidth - $rightLineMargin;
        $lineY1 = $y + $lineTopMargin;
        $lineY2 = $y + $h - $lineTopMargin;
        
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetDrawColor(150, 150, 150); // 灰色分隔線
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($rightLineX, $lineY1, $rightLineX, $lineY2);
        } else {
            $this->pdf->SetDrawColor(150, 150, 150); // 灰色分隔線
            $this->pdf->SetLineWidth(0.1);
            $this->pdf->Line($rightLineX, $lineY1, $rightLineX, $lineY2);
        }
        
        // 標題放在中間（在兩條分隔線之間）
        $titleStartX = $leftLineX + 5;
        $titleWidth = $rightLineX - $titleStartX - 5;
        
        if($title_sub == null){
            $this->pdf->setFont($titleFont, 'B', 22);
            $this->pdf->setXY($titleStartX, $y);
            $this->pdf->Cell($titleWidth, $h, $title, 0, 1, 'C', 0, '', 0);
        } else {
            $this->pdf->setFont($titleFont, 'B', 18);
            $this->pdf->setXY($titleStartX, $y);
            $this->pdf->Cell($titleWidth, $h / 1.6, $title, 0, 1, 'C', 0, '', 0);
            $this->pdf->setFont($titleFont, 'B', 12);
            $this->pdf->setXY($titleStartX, $y + ($h / 1.6));
            $this->pdf->Cell($titleWidth, $h - ($h / 1.6), $title_sub, 0, 0, 'C', 0, '', 0);
        }
        
        if ($ellipseData !== null) {
            $this->pdf->SetTextColor(0, 0, 0);
            
            $program = $ellipseData["program"];
            $weight = $ellipseData["weight"];
            $upper_limit = isset($ellipseData["upper_limit"]) ? $ellipseData["upper_limit"] : "";
            $athletes_count = isset($ellipseData["athletes_count"]) ? $ellipseData["athletes_count"] : "";
            
            // 組合 program 和 athletes_count
            $programText = $program;
            if (!empty($athletes_count)) {
                $programText .= " (" . $athletes_count . ")";
            }
            
            // 右側起始位置和寬度
            $rightStartX = $rightLineX + $rightLineMargin;
            $rightWidth = $rightContentWidth - $rightLineMargin * 2;
            
            // 計算垂直置中的 Y 位置
            $totalTextHeight = 14; // 兩行文字的總高度
            $startY = $y + ($h - $totalTextHeight) / 2;
            
            // 繪製右側內容（weight 和 upper_limit 在同一行，program 在下面）
            $this->pdf->setFont($titleFont, 'B', 18); // weight 字型
            
            // 如果有 upper_limit，則 weight 靠左對齊
            if (!empty($upper_limit)) {
                $this->pdf->SetXY($rightStartX, $startY);
                $this->pdf->Cell($rightWidth, 7, $weight, 0, 0, 'L'); // 左對齊
                
                // upper_limit 使用較小字型，靠右對齊，並向右移動更多
                $this->pdf->setFont($titleFont, 'B', 8); // upper_limit 使用小字型
                $this->pdf->SetXY($rightStartX + 17, $startY + 1); // 向右移動 10mm，稍微向下偏移以對齊基線
                $this->pdf->Cell($rightWidth - 10, 7, $upper_limit, 0, 0, 'R'); // 右對齊，寬度減少 10mm
            } else {
                // 如果沒有 upper_limit，weight 置中
                $this->pdf->SetXY($rightStartX, $startY);
                $this->pdf->Cell($rightWidth + 5, 7, $weight, 0, 0, 'C'); // 置中
            }
            
            $this->pdf->setFont($titleFont, 'B', 10); // program 字型
            $this->pdf->SetXY($rightStartX, $startY + 7);
            $this->pdf->Cell($rightWidth + 5, 7, $programText, 0, 0, 'C'); // 水平置中
        }
    }
public function header4($x = 0, $y = 0, $title = null, $title_sub = null, $logo_primary = null, $logo_secondary = null, $titleFont = 'times', $ellipseData = null)
{
    $w = 190;
    $h = 18;
    $r = 3;
    
    // 計算置中的 x 座標 (假設頁面寬度為 210mm A4)
    $pageWidth = 210;
    $centeredX = ($pageWidth - $w) / 2;
    
    // 先繪製圓角邊框作為 clipping path
    if (get_class($this->pdf) == 'Mpdf\Mpdf') {
        // 對於 Mpdf，使用 Save/Restore 來限制繪圖區域
        $this->pdf->StartTransform();
        $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'CNZ'); // CNZ 創建 clipping path
    } else {
        // 對於 TCPDF，使用 clipping
        $this->pdf->StartTransform();
        $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'CNZ');
    }
    
    // 創建更自然的漸變背景（現在會被限制在圓角內）
    $gradientWidth = $w;
    $gradientHeight = $h;
    $steps = 50; // 大幅增加漸變步數讓過渡更平滑
    
    // 定義漸變顏色 - 使用更和諧的銀白色調
    $silver = [220, 220, 220];  // 較亮的銀色
    $white = [255, 255, 255];   // 純白
    $lightSilver = [240, 240, 240]; // 過渡用的亮銀色
    
    // 繪製漸變背景（現在會被限制在圓角內）
    for ($i = 0; $i < $steps; $i++) {
        $stepX = $centeredX + ($gradientWidth / $steps) * $i;
        $stepWidth = $gradientWidth / $steps;
        
        // 計算漸變顏色 - 使用更平滑的過渡
        if ($i < $steps * 0.3) {
            // 左側 30%: 銀 → 亮銀
            $ratio = $i / ($steps * 0.3);
            $rColor = $silver[0] + ($lightSilver[0] - $silver[0]) * $ratio;
            $gColor = $silver[1] + ($lightSilver[1] - $silver[1]) * $ratio;
            $bColor = $silver[2] + ($lightSilver[2] - $silver[2]) * $ratio;
        } elseif ($i < $steps * 0.7) {
            // 中間 40%: 亮銀 → 白 → 亮銀
            $midRatio = ($i - $steps * 0.3) / ($steps * 0.4);
            if ($midRatio < 0.5) {
                // 前半: 亮銀 → 白
                $ratio = $midRatio * 2;
                $rColor = $lightSilver[0] + ($white[0] - $lightSilver[0]) * $ratio;
                $gColor = $lightSilver[1] + ($white[1] - $lightSilver[1]) * $ratio;
                $bColor = $lightSilver[2] + ($white[2] - $lightSilver[2]) * $ratio;
            } else {
                // 後半: 白 → 亮銀
                $ratio = ($midRatio - 0.5) * 2;
                $rColor = $white[0] - ($white[0] - $lightSilver[0]) * $ratio;
                $gColor = $white[1] - ($white[1] - $lightSilver[1]) * $ratio;
                $bColor = $white[2] - ($white[2] - $lightSilver[2]) * $ratio;
            }
        } else {
            // 右側 30%: 亮銀 → 銀
            $ratio = ($i - $steps * 0.7) / ($steps * 0.3);
            $rColor = $lightSilver[0] - ($lightSilver[0] - $silver[0]) * $ratio;
            $gColor = $lightSilver[1] - ($lightSilver[1] - $silver[1]) * $ratio;
            $bColor = $lightSilver[2] - ($lightSilver[2] - $silver[2]) * $ratio;
        }
        
        // 繪製漸變矩形（現在會被限制在圓角內）
        if (get_class($this->pdf) == 'Mpdf\Mpdf') {
            $this->pdf->SetFillColor($rColor, $gColor, $bColor);
            $this->pdf->Rect($stepX, $y, $stepWidth, $gradientHeight, 'F');
        } else {
            $this->pdf->SetFillColor($rColor, $gColor, $bColor);
            $this->pdf->Rect($stepX, $y, $stepWidth, $gradientHeight, 'F');
        }
    }
    
    // 結束 clipping
    $this->pdf->StopTransform();
    
    // 重新繪製邊框（在背景之上）
    if (get_class($this->pdf) == 'Mpdf\Mpdf') {
        $this->pdf->SetLineWidth(0.2);
        $this->pdf->SetDrawColor(180, 180, 180);
        $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, 'D');
    } else {
        $this->pdf->RoundedRect($centeredX, $y, $w, $h, $r, '1111', 'D', $this->boxLine, [180, 180, 180]);
    }
    
    // 圖片放在左邊
    $leftMargin = 5;
    $logoSpacing = 2; // 圖標之間的間距

    if ($logo_primary) {
        $this->pdf->image($logo_primary, $centeredX + $leftMargin, $y + 2, 14, 14, 'png');
    }
    if ($logo_secondary) {
        $primaryWidth = $logo_primary ? 14 + $logoSpacing : 0;
        $this->pdf->image($logo_secondary, $centeredX + $leftMargin + $primaryWidth, $y + 2, 14, 14, 'png');
    }
    
    // 計算 logo 佔用的總寬度
    $logoTotalWidth = 0;
    if ($logo_primary) {
        $logoTotalWidth += 14;
    }
    if ($logo_secondary) {
        $logoTotalWidth += $logo_primary ? 14 + $logoSpacing : 14;
    }
    
    // 計算標題區域的起始位置和寬度（更加置中）
    $titleStartX = $centeredX + $leftMargin + $logoTotalWidth; // logo 右邊加上間距
    $titleWidth = $w - ($leftMargin * 2) - $logoTotalWidth -10; // 減去左右邊距和 logo 寬度
    
    if($title_sub == null){
        $this->pdf->setFont($titleFont, 'B', 24);
        $this->pdf->setXY($titleStartX, $y);
        $this->pdf->Cell($titleWidth, $h / 1, $title, 0, 1, 'C', 0, '', 0);
    } else {
        $this->pdf->setFont($titleFont, 'B', 20);
        $this->pdf->setXY($titleStartX, $y);
        $this->pdf->Cell($titleWidth, $h / 1.6, $title, 0, 1, 'C', 0, '', 0);
        $this->pdf->setFont($titleFont, 'B', 13);
        $this->pdf->setXY($titleStartX, $y + ($h / 1.6));
        $this->pdf->Cell($titleWidth, $h - ($h / 1.6), $title_sub, 0, 0, 'C', 0, '', 0);
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
