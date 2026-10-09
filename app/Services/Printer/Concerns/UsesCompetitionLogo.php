<?php

namespace App\Services\Printer\Concerns;

use App\Models\Competition;

/**
 * 讓列印服務的 LOGO 改用「賽事儲存的 LOGO」。
 *
 * 使用方式：
 *   class XxxService { use UsesCompetitionLogo; }
 *   $service->setCompetitionLogo($competition);
 *
 * 賽事沒有上傳 LOGO 時，會保留服務原本的預設圖（並自動轉成絕對路徑，
 * 因為 mPDF 不接受相對專案根目錄的路徑）。
 */
trait UsesCompetitionLogo
{
    /**
     * @param  Competition|null  $competition  賽事（null 時只把原本的預設圖正規化）
     * @param  string|null  $secondary  第二個 LOGO；null = 沿用服務原本設定
     */
    public function setCompetitionLogo(?Competition $competition, ?string $secondary = null): static
    {
        $this->logo_primary = $competition
            ? $competition->logoPath($this->logo_primary)
            : $this->absoluteLogoPath($this->logo_primary);

        if ($secondary !== null) {
            $this->logo_secondary = $this->absoluteLogoPath($secondary);
        }

        return $this;
    }

    /**
     * 把相對 public 的路徑轉成絕對路徑（已經是絕對路徑或 URL 就原樣回傳）。
     */
    protected function absoluteLogoPath(?string $path): ?string
    {
        if (!$path || Competition::isAbsolutePath($path)) {
            return $path;
        }

        return str_replace('\\', '/', public_path($path));
    }
}
