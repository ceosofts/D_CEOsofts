<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force Accept header to application/json
        $request->headers->set('Accept', 'application/json');
        
        // Process the request
        $response = $next($request);
        
        // If the response is not already a JSON response, wrap it
        if (!$this->isJsonResponse($response) && !$this->isFileResponse($response)) {
            $originalContent = $response->getContent();
            
            $data = [
                'success' => $response->isSuccessful(),
                'status' => $response->getStatusCode(),
                'data' => $this->isJson($originalContent) ? json_decode($originalContent) : $originalContent,
            ];
            
            if (!$response->isSuccessful() && !empty($originalContent)) {
                $data['message'] = $this->isJson($originalContent) 
                    ? json_decode($originalContent)->message ?? 'Error occurred'
                    : 'Error occurred';
            }
            
            $response->setContent(json_encode($data));
            $response->header('Content-Type', 'application/json');
        }
        
        return $response;
    }
    
    /**
     * Check if the response is already a JSON response
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return bool
     */
    protected function isJsonResponse($response): bool
    {
        return $response->headers->get('Content-Type') === 'application/json';
    }
    
    /**
     * Check if the response is a file download
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return bool
     */
    protected function isFileResponse($response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        $contentDisposition = $response->headers->get('Content-Disposition');
        
        return $contentDisposition !== null && 
               (strpos($contentDisposition, 'attachment') !== false || 
                strpos($contentDisposition, 'inline') !== false);
    }
    
    /**
     * Check if a string is valid JSON
     *
     * @param  string  $string
     * @return bool
     */
    protected function isJson($string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
