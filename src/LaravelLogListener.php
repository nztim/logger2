<?php namespace NZTim\Logger;

use Illuminate\Log\Events\MessageLogged;

class LaravelLogListener
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handle(MessageLogged $messageLogged)
    {
        $this->logger->add('laravel', $this->logger->translateLevel($messageLogged->level), $messageLogged->message, $messageLogged->context);
    }
}
