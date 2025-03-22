<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class AssetHelper
{
    /**
     * Get the appropriate asset URL depending on environment
     * Falls back to CDN versions if Vite manifest is not found
     *
     * @param string $asset
     * @return string
     */
    public static function asset($asset)
    {
        // Check if we're in development and not using Vite
        if (!File::exists(public_path('build/manifest.json'))) {
            // Return CDN URL for common libraries
            if (str_contains($asset, 'app.css')) {
                return 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
            }
            
            if (str_contains($asset, 'app.js')) {
                return 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
            }
            
            // Return a local fallback file if it exists
            $fallbackPath = public_path('fallback/' . basename($asset));
            if (File::exists($fallbackPath)) {
                return asset('fallback/' . basename($asset));
            }
            
            // Last resort - return the asset as is
            return asset($asset);
        }
        
        // Use Vite if manifest exists
        return asset('build/' . $asset);
    }
    
    /**
     * Output the appropriate script or link tags for assets
     * 
     * @return string
     */
    public static function viteAssets()
    {
        $html = '';
        
        // Check if Vite manifest exists
        if (File::exists(public_path('build/manifest.json'))) {
            // Use Vite directive
            $html .= '@vite([\'resources/css/app.css\', \'resources/js/app.js\'])';
        } else {
            // Fallback to CDN versions
            $html .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
            $html .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
            
            // jQuery (needed for our app)
            $html .= '<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>';
            
            // Add custom fallback CSS if available
            if (File::exists(public_path('fallback/app.css'))) {
                $html .= '<link href="' . asset('fallback/app.css') . '" rel="stylesheet">';
            }
            
            // Add custom fallback JS if available
            if (File::exists(public_path('fallback/app.js'))) {
                $html .= '<script src="' . asset('fallback/app.js') . '"></script>';
            }
        }
        
        return $html;
    }
}
