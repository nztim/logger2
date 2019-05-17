<?php

use \NZTim\Logger\Logger;

if (!function_exists('log_info')) {
    function log_info(...$args): void
    {
        app(Logger::class)->info(...$args);
    }
}

if (!function_exists('log_warning')) {
    function log_warning(...$args): void
    {
        app(Logger::class)->warning(...$args);
    }
}

if (!function_exists('log_error')) {
    function log_error(...$args): void
    {
        app(Logger::class)->error(...$args);
    }
}
