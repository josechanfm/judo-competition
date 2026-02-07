<?php

namespace App\Services;

use TCPDF;

class CustomTCPDF extends TCPDF
{
    public function Footer()
    {
        // 位置從底部向上 15mm
        $this->SetY(-10);
        // 設置字體
        $this->SetFont('helvetica', '', 10);
        // 設置頁碼置中
        $this->Cell(0, 10, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}