<?php namespace NZTim\Logger;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Swift_Mailer;
use Swift_Message;
use Throwable;

class Logger
{
    protected $config;
    protected $swiftMailer;
    protected $cache;

    public function __construct(array $config, Swift_Mailer $swiftMailer, Cache $cache)
    {
        $this->config = $config;
        $this->swiftMailer = $swiftMailer;
        $this->cache = $cache;
    }

    public function info(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::INFO, $message, $context);
    }

    public function notice(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::NOTICE, $message, $context);
    }

    public function warning(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::WARNING, $message, $context);
    }

    public function error(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::ERROR, $message, $context);
    }

    public function critical(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::CRITICAL, $message, $context);
    }

    public function alert(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::ALERT, $message, $context);
    }

    public function emergency(string $channel, string $message, array $context = [])
    {
        $this->add($channel, MonologLogger::EMERGENCY, $message, $context);
    }

    public function add(string $channel, int $level, string $message, array $context = [])
    {
        $channel = $this->cleanChannelName($channel);
        $logger = new MonologLogger($channel);
        $this->addEmailHandler($logger, $level);
        $this->addLogFileHandler($logger, $channel);
        try {
            $logger->log($level, $message, $context);
        } catch (Throwable $e) {
            $this->writeExceptionMessage($e->getMessage(), $message);
        }
    }

    protected function cleanChannelName(string $channel): string
    {
        // Regex: remove all chars not a-z,A-Z,0-9
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $channel)));
    }

    protected function addEmailHandler(MonologLogger $logger, int $level)
    {
        $key = 'nztim-logger-throttle';
        if ($this->config['email']['send'] && $level >= MonologLogger::ERROR && !$this->cache->has($key)) {
            $swiftMessage = (new Swift_Message)
                ->setSubject('Log notification from: ' . $this->config['name'])
                ->setFrom($this->config['email']['from'])
                ->setTo($this->config['email']['to'])
                ->setContentType('text/html');
            $handler = new SwiftMailerHandler($this->swiftMailer, $swiftMessage);
            $handler->setFormatter(new HtmlFormatter());
            $logger->pushHandler($handler);
            $this->cache->put($key, true, 5);
        }
    }

    protected function addLogFileHandler(MonologLogger $logger, string $channel)
    {
        if (in_array($channel, $this->config['daily'])) {
            $logger->pushHandler(new RotatingFileHandler($this->filename($channel), $this->config['max_daily']));
            return;
        }
        $logger->pushHandler(new StreamHandler($this->filename($channel)));
    }

    protected function filename($channel)
    {
        $s = DIRECTORY_SEPARATOR;
        return  "{$this->config['log_path']}{$s}custom{$s}{$channel}.log";
    }

    protected function writeExceptionMessage(string $error, string $message)
    {
        $message = date('c') . " Logger exception: {$error}\nMessage: " . $message;
        $s = DIRECTORY_SEPARATOR;
        $filename = "{$this->config['log_path']}{$s}fatal-logger-errors.log";
        file_put_contents($filename, $message, FILE_APPEND);
    }

    public function translateLevel(string $level): int
    {
        $level = strtoupper($level);
        return array_key_exists($level, MonologLogger::getLevels()) ? MonologLogger::getLevels()[$level] : MonologLogger::ERROR;
    }
}
