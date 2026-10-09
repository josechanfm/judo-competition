<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\PdfHelper;
use App\Models\Program;
use App\Services\CustomTCPDF;
use App\Services\Printer\AthleteWeighInService;

$stamp = PdfHelper::printTimestamp();
$size = PdfHelper::TIMESTAMP_FONT_SIZE;
$grey = PdfHelper::timestampColor();
echo "共用樣式: font=" . PdfHelper::TIMESTAMP_FONT . " size={$size}pt color={$grey}\n\n";

$streams = function (string $bytes): array {
    $out = [];
    if (preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $bytes, $m)) {
        foreach ($m[1] as $s) {
            $d = @gzuncompress($s);
            $out[] = $d !== false ? $d : $s;
        }
    }
    return $out;
};

$check = function (array $streams, string $stamp, float $expectedSize) {
    $time = $sizeOk = $colorOk = false;
    foreach ($streams as $s) {
        if (strpos($s, $stamp) !== false || stripos($s, bin2hex($stamp)) !== false) {
            $time = true;
        }
        if (preg_match_all('/([\d.]+) Tf/', $s, $m)) {
            foreach ($m[1] as $v) {
                if (abs((float) $v - $expectedSize) < 0.02) {
                    $sizeOk = true;
                }
            }
        }
        if (preg_match('/(0\.\d+) (?:0\.\d+ )?0?\.?\d* ?0?\.?\d* rg/', $s) && preg_match('/0\.4[67]\d* 0\.4[67]\d* 0\.4[67]\d* rg/', $s)) {
            $colorOk = true;
        }
    }
    return [$time, $sizeOk, $colorOk];
};

// 1) TCPDF（CustomTCPDF::Footer）
$pdf = new CustomTCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 40, 15);
$pdf->setPrintHeader(false);
$pdf->AddPage();
$pdf->SetFont('notoserifcjkhk', '', 12);
$pdf->Cell(0, 10, 'content', 0, 1, 'L');
[$t, $s, $c] = $check($streams($pdf->Output('', 'S')), $stamp, $size);
printf("TCPDF (CustomTCPDF::Footer): time=%s size=%s color=%s -> %s\n", $t ? 'Y' : 'N', $s ? 'Y' : 'N', $c ? 'Y' : 'N', ($t && $s && $c) ? 'PASS' : 'FAIL');

// 2) mPDF（PdfHelper::footerHtml）
$mpdfHtml = PdfHelper::footerHtml();
$mpdf = new \Mpdf\Mpdf();
$mpdf->SetHTMLFooter($mpdfHtml);
$mpdf->AddPage();
$mpdf->WriteHTML('<p>content</p>');
$mpdfBytes = $mpdf->Output('', 'S');
$mpdfStreams = $streams($mpdfBytes);
[$t, $s, $c] = $check($mpdfStreams, $stamp, $size);
printf("mPDF (PdfHelper::footerHtml): time=%s size=%s color=%s -> %s\n", $t ? 'Y' : 'N', $s ? 'Y' : 'N', $c ? 'Y' : 'N', ($t && $s && $c) ? 'PASS' : 'FAIL');

// 用「HTML 內實際的時間字串」當 needle，排除跨分鐘的時序問題
preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2})/', $mpdfHtml, $mm);
$needle = $mm[1] ?? '';
echo "  needle from HTML = {$needle}\n";
$hitStream = false;
foreach ($mpdfStreams as $i => $st) {
    if ($needle !== '' && strpos($st, $needle) !== false) {
        $hitStream = true;
    }
    $p = strpos($st, 'Tj');
    if ($p !== false && $i === 0) {
        echo '  DEBUG bytes before Tj: ' . bin2hex(substr($st, $p - 40, 40)) . "\n";
        echo '  DEBUG expected ASCII : ' . bin2hex('(' . $needle . ')') . "\n";
        echo '  DEBUG expected UTF16 : ' . bin2hex(implode('', array_map(fn ($ch) => "\x00" . $ch, str_split('(' . $needle . ')')))) . "\n";
    }
}
printf(
    "  mPDF text present: raw-file=%s decompressed=%s -> %s\n",
    strpos($mpdfBytes, $needle) !== false ? 'Y' : 'N',
    $hitStream ? 'Y' : 'N',
    ($hitStream || strpos($mpdfBytes, $needle) !== false) ? 'PASS' : 'FAIL'
);

// 3) 過磅表 end-to-end
$programs = Program::with('competitionCategory')->has('athletes')->take(3)->get();
ob_start();
$service = new AthleteWeighInService();
$service->setTitle('測試賽事', null);
$weighBytes = $service->generateAllWeighInTable($programs)->Output('', 'S');
ob_end_clean();
preg_match_all('/\/Type\s*\/Page[^s]/', $weighBytes, $pm);
$pageCount = count($pm[0]);
$hits = 0;
foreach ($streams($weighBytes) as $s) {
    if (strpos($s, $stamp) !== false || stripos($s, bin2hex($stamp)) !== false) {
        $hits++;
    }
}
printf("過磅表 end-to-end: %d timestamp / %d page -> %s\n", $hits, $pageCount, ($pageCount > 0 && $hits === $pageCount) ? 'PASS' : 'FAIL');

// 4) 靜態檢查：表格服務是否都已改用 CustomTCPDF
echo "\n--- 靜態檢查 ---\n";
$converted = ['AthleteWeighInService', 'CompetitionResultService', 'TeamAthletesService', 'RoundRobbinOption1Service', 'TournamentService', 'TournamentDoubleService', 'TournamentFullService', 'WinnerService', 'TournamentQuarterService', 'RoundRobbinOption2Service'];
foreach ($converted as $name) {
    $src = file_get_contents("app/Services/Printer/{$name}.php");
    $ok = strpos($src, 'new CustomTCPDF(') !== false && !preg_match('/^\s*\$this->pdf = new TCPDF\(/m', $src);
    printf("  %-28s %s\n", $name, $ok ? 'CustomTCPDF OK' : 'FAIL');
}
foreach (['DelegationService', 'RefereeService', 'WeightInService'] as $name) {
    $src = file_get_contents("app/Services/Printer/{$name}.php");
    printf("  %-28s %s\n", $name, strpos($src, 'SetHTMLFooter(PdfHelper::footerHtml())') !== false ? 'mPDF footer OK' : 'FAIL');
}
$src = file_get_contents('app/Services/Printer/ProgramScheduleService.php');
printf("  %-28s %s\n", 'ProgramScheduleService', strpos($src, 'return PdfHelper::footerHtml();') !== false ? 'mPDF footer OK' : 'FAIL');
