<?php

namespace App\Services\Printer;

use App\Helpers\PdfHelper;
use App\Services\CustomTCPDF;
use App\Services\Printer\Concerns\UsesCompetitionLogo;
use Illuminate\Support\Collection;

/**
 * 運動員簽到表。
 *
 * 每一個「日期 + 場地 + 時段」一個頁面，列出該時段要出賽的運動員，
 * 讓運動員在「簽到」欄簽名（未過磅的仍會列出，只有明確過磅失敗的被排除）。
 */
class AthleteCheckInService
{
    use UsesCompetitionLogo;

    /** 表格欄寬（mm）：序號 / 隊伍 / 姓名 / 外文姓名 / 組別 / 公斤級 / 簽到 */
    private const COLUMN_WIDTHS = [10, 28, 34, 32, 30, 26, 20];
    private const HEADERS = ['序號', '隊伍', '姓名', '外文姓名', '組別', '公斤級', '簽到'];

    private const ROW_FONT_SIZE = 9;   // 列文字字級（太寬的儲存格會自動再縮小）
    /** 內容過寬時依序嘗試的字級 */
    private const FIT_FONT_SIZES = [9, 8, 7, 6];

    private const TABLE_LEFT = 15;
    private const TABLE_WIDTH = 180;   // = A4 直向可用寬度（210 - 15 - 15）
    private const TABLE_TOP = 48;      // 表格起始 Y
    private const TABLE_BOTTOM = 275;  // 超過就換頁（統一頁尾畫在 287 以下，不會重疊）
    private const HEADER_HEIGHT = 9;
    private const ROW_HEIGHT = 9;

    private $pdf;
    private ?string $title = null;
    private ?string $title_sub = null;
    private string $logo_primary = '';
    private ?string $logo_secondary = null;
    private string $titleFont = 'notoserifcjkhk';

    public function __construct()
    {
        $this->pdf = new CustomTCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetCreator('Sports Club');
        $this->pdf->SetAuthor('Sports Club');
        $this->pdf->SetTitle('Athlete Check-in List');
        $this->pdf->SetMargins(self::TABLE_LEFT, 40, 15);
        // 分頁自己控制，避免 TCPDF 自動換頁打亂表格版面
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(true);
    }

    public function setTitle($title, $subtitle = null): static
    {
        $this->title = $title;
        if ($subtitle) {
            $this->title_sub = $subtitle;
        }

        return $this;
    }

    public function setTitleFont(string $font): static
    {
        $this->titleFont = $font;

        return $this;
    }

    /**
     * @param  Collection  $groups  [['date' =>, 'mat' =>, 'section' =>, 'programs' => Collection], ...]
     */
    public function generate(Collection $groups)
    {
        foreach ($groups as $group) {
            $rows = $this->buildRows($group['programs'] ?? collect());

            // 該時段沒有要簽到的運動員就不印
            if (empty($rows)) {
                continue;
            }

            $this->addGroup($group, $rows);
        }

        return $this->pdf;
    }

    /**
     * 把項目轉成表格列資料。過磅失敗（is_weight_passed = 0）的不列入，未過磅（null）的仍列入。
     */
    private function buildRows(Collection $programs): array
    {
        $rows = [];

        foreach ($programs as $program) {
            foreach ($program->programAthletes as $programAthlete) {
                if (!is_null($programAthlete->is_weight_passed) && (int) $programAthlete->is_weight_passed === 0) {
                    continue;
                }

                $athlete = $programAthlete->athlete;

                if (!$athlete) {
                    continue;
                }

                $rows[] = [
                    // 隊伍用簡稱，沒有簡稱才用全名
                    'team' => $athlete->team->abbreviation ?? ($athlete->team->name ?? ''),
                    'name' => $this->smartTruncate($athlete->name ?? ''),
                    'name_secondary' => $this->smartTruncate($athlete->name_secondary ?? ''),
                    'category' => $program->competitionCategory->name ?? '',
                    'weight' => $program->convertGender() . $program->convertWeight(),
                ];
            }
        }

        return $rows;
    }

    /**
     * 一個時段（含分頁）的所有頁面。
     */
    private function addGroup(array $group, array $rows): void
    {
        $rowsPerPage = (int) floor((self::TABLE_BOTTOM - self::TABLE_TOP - self::HEADER_HEIGHT) / self::ROW_HEIGHT);
        $rowsPerPage = max(1, $rowsPerPage);

        $pageCount = (int) ceil(count($rows) / $rowsPerPage);

        foreach (array_chunk($rows, $rowsPerPage) as $index => $pageRows) {
            $this->pdf->AddPage();

            // 「續」只在同一個時段的第二頁之後才印
            $this->drawGroupHeader(
                $group,
                $index > 0 ? '（續）' : '',
                $pageCount > 1 ? $index + 1 : 0,
                $pageCount
            );
            $this->drawTableHeader();
            $this->drawRows($pageRows, $index * $rowsPerPage);
        }
    }

    /**
     * 頁首：賽事標題 + 簽到表標題 + 「日期 場地 時段」。
     */
    private function drawGroupHeader(array $group, string $suffix = '', int $pageNumber = 0, int $pageCount = 0): void
    {
        $helper = new PdfHelper($this->pdf);
        $helper->header4(12, 5, $this->title, $this->title_sub, $this->logo_primary, $this->logo_secondary, $this->titleFont);

        $this->pdf->SetFont($this->titleFont, 'B', 15);
        $this->pdf->SetXY(self::TABLE_LEFT, 30);
        $this->pdf->Cell(self::TABLE_WIDTH, 8, '運動員簽到表' . $suffix, 0, 1, 'C');

        $subtitle = '日期：' . ($group['date'] ?? '-')
            . '　場地：' . ($group['mat'] ?? '-')
            . '　時段：第' . ($group['section'] ?? '-') . '時段';

        if ($pageNumber > 0) {
            $subtitle .= '　（第 ' . $pageNumber . '/' . $pageCount . ' 頁）';
        }

        $this->pdf->SetFont($this->titleFont, '', 11);
        $this->pdf->SetXY(self::TABLE_LEFT, 38);
        $this->pdf->Cell(self::TABLE_WIDTH, 6, $subtitle, 0, 1, 'C');
    }

    private function drawTableHeader(): void
    {
        $this->pdf->SetFont($this->titleFont, 'B', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFillColor(220, 220, 220);
        $this->pdf->SetXY(self::TABLE_LEFT, self::TABLE_TOP);

        foreach (self::HEADERS as $index => $header) {
            $this->pdf->Cell(self::COLUMN_WIDTHS[$index], self::HEADER_HEIGHT, $header, 1, 0, 'C', true);
        }

        $this->pdf->Ln();
    }

    private function drawRows(array $rows, int $startIndex): void
    {
        $this->pdf->SetTextColor(0, 0, 0);

        foreach ($rows as $offset => $row) {
            $cells = [
                (string) ($startIndex + $offset + 1),
                $row['team'],
                $row['name'],
                $row['name_secondary'],
                $row['category'],
                $row['weight'],
                '', // 簽到欄留空
            ];

            $this->pdf->SetX(self::TABLE_LEFT);

            foreach ($cells as $index => $value) {
                $align = in_array($index, [0, 5], true) ? 'C' : 'L';

                $this->pdf->Cell(
                    self::COLUMN_WIDTHS[$index],
                    self::ROW_HEIGHT,
                    $this->fit($value, self::COLUMN_WIDTHS[$index]),
                    1,
                    0,
                    $align
                );
            }

            $this->pdf->Ln();
        }
    }

    /**
     * 外文姓名縮寫（與過磅表 `AthleteWeighInService::smartTruncate` 相同邏輯）：
     * 太長時把前面的名字縮成首字母，例如 Vella Ankundinova → V. Ankundinova；
     * 仍太長才只留「第一個名字首字母 + 姓氏」。
     */
    private function smartTruncate(?string $name, int $maxLength = 20): string
    {
        $name = trim((string) $name);

        if ($name === '' || mb_strlen($name) <= $maxLength) {
            return $name;
        }

        $parts = preg_split('/\s+/', $name) ?: [];

        if (count($parts) >= 2) {
            // 前面的名字只留首字母：Vella Ankundinova → V. Ankundinova
            $shortName = '';
            for ($i = 0; $i < count($parts) - 1; $i++) {
                if ($parts[$i] !== '') {
                    $shortName .= mb_substr($parts[$i], 0, 1) . '.';
                }
            }
            $shortName .= ' ' . end($parts);

            if (mb_strlen($shortName) <= $maxLength) {
                return $shortName;
            }

            // 還是太長：第一個名字首字母 + 姓氏
            $simplest = mb_substr($parts[0], 0, 1) . '. ' . end($parts);

            if (mb_strlen($simplest) <= $maxLength) {
                return $simplest;
            }
        }

        return mb_substr($name, 0, $maxLength - 1) . '…';
    }

    /**
     * 內容太寬時先縮字級（9 → 8 → 7 → 6），真的還是太寬才依比例截斷。
     * 每次呼叫都從列字級重設，所以同一列的每個儲存格都各自獨立判斷。
     */
    private function fit(string $text, float $columnWidth): string
    {
        $text = trim($text);
        $available = $columnWidth - 2; // 左右各留 1mm 內距

        if ($text === '') {
            $this->pdf->SetFont($this->titleFont, '', self::ROW_FONT_SIZE);

            return '';
        }

        foreach (self::FIT_FONT_SIZES as $size) {
            $this->pdf->SetFont($this->titleFont, '', $size);

            if ($this->pdf->GetStringWidth($text) <= $available) {
                return $text;
            }
        }

        $width = $this->pdf->GetStringWidth($text);
        $maxChars = (int) floor(mb_strlen($text) * $available / $width);

        return mb_substr($text, 0, max(1, $maxChars)) . '…';
    }
}
