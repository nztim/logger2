<?php namespace NZTim\Logger;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Swift_Mailer;
use Swift_SmtpTransport;

class LoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/logger_config.php' => config_path('logger.php')]);
        if (config('logger.laravel', false)) {
            Event::listen(MessageLogged::class, LaravelLogListener::class);
        }
    }

    public function register()
    {
        $this->app->bind(Logger::class, function () {
            $transport = (new Swift_SmtpTransport)
                ->setHost(config('mail.host'))
                ->setPort(config('mail.port'))
                ->setEncryption(config('mail.encryption'))
                ->setUsername(config('mail.username'))
                ->setPassword(config('mail.password'));
            $mailer = new Swift_Mailer($transport);
            $config = config('logger');
            $config['name'] = isset($config['name']) ? $config['name'] : config('app.name');
            $config['log_path'] = isset($config['log_path']) ? $config['log_path'] : storage_path('logs');
            if (config('app.debug')) {
                $config['email']['send'] = false;
            }
            return new Logger($config, $mailer, app(LaravelCache::class));
        });
        $this->mergeConfigFrom(__DIR__.'/logger_config.php', 'logger');
    }
}
