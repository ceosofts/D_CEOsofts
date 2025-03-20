<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ThaiPdfService
{
    /**
     * Generate PDF with Thai language support
     * 
     * @param string $html HTML content
     * @param array $options PDF options
     * @return mixed
     */
    public function generatePdf(string $html, array $options = [])
    {
        $defaultOptions = [
            'paper' => 'a4',
            'orientation' => 'portrait',
            'enable_php' => true,
            'enable_remote' => true,
            'enable_javascript' => true,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            return Pdf::loadHTML($html)
                ->setPaper($options['paper'], $options['orientation'])
                ->setOption('enable_php', $options['enable_php'])
                ->setOption('enable_remote', $options['enable_remote']) 
                ->setOption('enable_javascript', $options['enable_javascript'])
                ->setOption('margin_left', $options['margin_left'])
                ->setOption('margin_right', $options['margin_right'])
                ->setOption('margin_top', $options['margin_top'])
                ->setOption('margin_bottom', $options['margin_bottom'])
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true)
                ->setOption('defaultFont', 'thsarabunnew')
                ->stream('document.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate PDF and save to disk
     * 
     * @param string $html HTML content
     * @param string $path Save path
     * @param array $options PDF options
     * @return bool
     */
    public function savePdf(string $html, string $path, array $options = [])
    {
        $defaultOptions = [
            'paper' => 'a4',
            'orientation' => 'portrait',
            'enable_php' => true
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            Pdf::loadHTML($html)
                ->setPaper($options['paper'], $options['orientation'])
                ->setOption('enable_php', $options['enable_php'])
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true)
                ->setOption('defaultFont', 'thsarabunnew')
                ->save($path);
                
            return true;
        } catch (\Exception $e) {
            Log::error('PDF Save Error: ' . $e->getMessage());
            return false;
        }
    }
}
