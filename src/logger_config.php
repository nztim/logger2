<?php

return [

    // Capture Laravel log entries
    'laravel'   => true,

    // Log file names that are to be handled as daily logs
    'daily'     => [],

    // Maximum number of daily log files to keep
    'max_daily' => 7,

    // Email notifications
    'email'     => [
        'send'  => false,       // Sending of error emails
        'from'  => env('LOGGER_EMAIL_SENDER', 'sender@example.com'),
        'to'    => env('LOGGER_EMAIL_RECIPIENT', 'recipient@example.com'),
    ],

    // Set automatically in Laravel Service Provider:

    // Application name
    // 'name'      => 'MyApp',

    // Log path
    // 'log_path'  => storage_path('logs'),
];

