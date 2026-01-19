<?php

namespace App\Services;

use TCPDF_FONTS;

class FontService
{
    protected $fontsDir;
    
    public function __construct()
    {
        $this->fontsDir = base_path('vendor/tecnickcom/tcpdf/fonts/');
    }
    
    /**
     * 添加字体到TCPDF
     */
    public function addFont($fontPath, $fontName = null)
    {
        $fullPath = base_path($fontPath);
        
        if (!file_exists($fullPath)) {
            throw new \Exception("字体文件不存在: {$fullPath}");
        }
        
        $fontname = TCPDF_FONTS::addTTFfont($fullPath);
        
        return $fontname;
    }
    
    /**
     * 获取所有已安装字体
     */
    public function getInstalledFonts()
    {
        $files = glob($this->fontsDir . '*.php');
        $fonts = [];
        
        foreach ($files as $file) {
            $fontName = pathinfo($file, PATHINFO_FILENAME);
            if ($fontName !== 'uni2cid_aj16' && $fontName !== 'cid0ct') {
                $fonts[] = $fontName;
            }
        }
        
        return $fonts;
    }
}