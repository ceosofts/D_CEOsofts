<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoggingService
{
    /**
     * Log system activity
     * 
     * @param string $action Action performed
     * @param string $entity Entity type affected
     * @param int|string|null $entityId ID of the affected entity
     * @param array $data Additional data
     * @param string $channel Log channel
     * @return void
     */
    public static function activity(
        string $action,
        string $entity,
        $entityId = null,
        array $data = [],
        string $channel = 'activity'
    ): void {
        $user = Auth::user();
        
        $logData = [
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'data' => $data,
        ];
        
        Log::channel($channel)->info(json_encode($logData));
    }
    
    /**
     * Log application error
     * 
     * @param \Throwable $exception The exception/error to log
     * @param string $context Context description
     * @param string $channel Log channel
     * @return string Error reference code
     */
    public static function error(
        \Throwable $exception,
        string $context = '',
        string $channel = 'errors'
    ): string {
        $user = Auth::user();
        $errorCode = strtoupper(Str::random(8));
        
        $logData = [
            'error_code' => $errorCode,
            'context' => $context,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => $user ? $user->id : null,
            'ip_address' => request()->ip(),
            'request_url' => request()->fullUrl(),
            'request_method' => request()->method(),
            'request_params' => json_encode(request()->except(['password', 'password_confirmation'])),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];
        
        Log::channel($channel)->error("Error [{$errorCode}]: " . $exception->getMessage(), $logData);
        
        return $errorCode;
    }
    
    /**
     * Log database query
     * 
     * @param string $query SQL query
     * @param array $bindings Query bindings
     * @param float $time Execution time in ms
     * @param string $channel Log channel
     * @return void
     */
    public static function query(
        string $query,
        array $bindings = [],
        float $time = 0.0,
        string $channel = 'query'
    ): void {
        // Only log slow queries in production
        if (app()->environment('production') && $time < config('logging.slow_query_threshold', 1000)) {
            return;
        }
        
        $logData = [
            'query' => $query,
            'bindings' => $bindings,
            'time_ms' => round($time, 2),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'connection' => DB::connection()->getName(),
        ];
        
        Log::channel($channel)->info("Query executed in {$time}ms", $logData);
    }
    
    /**
     * Log security events
     * 
     * @param string $event Security event description
     * @param array $data Additional data
     * @param string $level Log level
     * @param string $channel Log channel
     * @return void
     */
    public static function security(
        string $event,
        array $data = [],
        string $level = 'warning',
        string $channel = 'security'
    ): void {
        $user = Auth::user();
        
        $logData = [
            'event' => $event,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Guest',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'data' => $data,
        ];
        
        Log::channel($channel)->{$level}($event, $logData);
    }
    
    /**
     * Log performance metrics
     * 
     * @param string $operation Operation being measured
     * @param float $timeMs Time in milliseconds
     * @param array $metrics Additional metrics
     * @param string $channel Log channel
     * @return void
     */
    public static function performance(
        string $operation,
        float $timeMs,
        array $metrics = [],
        string $channel = 'performance'
    ): void {
        $logData = [
            'operation' => $operation,
            'execution_time_ms' => round($timeMs, 2),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
            'metrics' => $metrics,
        ];
        
        // Only log slow operations in production
        if (app()->environment('production') && $timeMs < config('logging.slow_operation_threshold', 500)) {
            return;
        }
        
        Log::channel($channel)->info("Operation '{$operation}' executed in {$timeMs}ms", $logData);
    }
}
