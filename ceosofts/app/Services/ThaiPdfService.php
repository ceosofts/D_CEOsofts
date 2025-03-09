<?php

namespace App\Services;

use Mpdf\Mpdf;

class ThaiPdfService
{
    protected $mpdf;

    public function __construct()
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'thsarabunnew',
            'default_font_size' => 16,
            'tempDir' => storage_path('temp'),
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts')
            ]),
            'fontdata' => [
                'thsarabunnew' => [
                    'R' => 'THSarabunNew.ttf',
                    'useOTL' => 0x00,
                ]
            ]
        ]);

        $this->mpdf->autoScriptToLang = true;
        $this->mpdf->autoLangToFont = true;
        $this->mpdf->SetDisplayMode('fullpage');
    }

    public function generatePdf($html)
    {
        $this->mpdf->WriteHTML($html);
        return $this->mpdf->Output('document.pdf', 'I');
    }
}
