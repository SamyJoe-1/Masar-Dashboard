<?php
namespace App\Log;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function logError($exception)
    {
        Log::channel('laravel.log')->error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'time' => now()->toDateTimeString(),
        ]);
    }
}
