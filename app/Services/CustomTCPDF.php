<?php

namespace App\Services;

use App\Helpers\PdfHelper;
use TCPDF;

class CustomTCPDF extends TCPDF
{
    /**
     * 這份 PDF 的產生時間（每頁顯示相同時間，第一次畫 footer 時決定）
     */
    protected ?string $generatedAt = null;

    /**
     * 頁腳時間戳使用的時區。
     * app 的 timezone 是 UTC，但列印出來要跟現場掛鐘一致（澳門 UTC+8），
     * 之後如果換地區直接改這裡即可。
     */
    protected string $timestampTimezone = 'Asia/Macau';

    public function Footer()
    {
        // 位置從底部向上（單位跟文件一致）
        $this->SetY(-10);

        // 【統一樣式】字型 / 大小 / 顏色：全部表格共用 PdfHelper 的常數
        $this->SetTextColor(...PdfHelper::TIMESTAMP_COLOR);
        $this->SetFont(PdfHelper::TIMESTAMP_FONT, '', PdfHelper::TIMESTAMP_FONT_SIZE);

        // 左下角：產生時間（畫完把游標還原，不影響原本頁碼的置中位置）
        $x = $this->GetX();
        $y = $this->GetY();

        $this->SetX($this->getMargins()['left']);
        $this->Cell(0, 10, $this->generatedAt(), 0, false, 'L', 0, '', 0, false, 'T', 'M');

        $this->SetXY($x, $y);

        // 頁碼置中
        $this->Cell(0, 10, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    /**
     * 產生時間文字（用純 ASCII 格式，footer 的 helvetica 字型才不會出現缺字）
     */
    protected function generatedAt(): string
    {
        if ($this->generatedAt === null) {
            $this->generatedAt = PdfHelper::printTimestamp($this->timestampTimezone);
        }

        return $this->generatedAt;
    }
}