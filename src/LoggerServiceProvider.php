<?php namespace NZTim\Logger;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\ServiceProvider;
use Swift_Mailer;
use Swift_SmtpTransport;

class LoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/logger_config.php' => config_path('logger.php')]);
        if (config('logger.laravel', false)) {
            $this->app->get(\Illuminate\Events\Dispatcher::class)->listen(MessageLogged::class, LaravelLogListener::class);
        }
    }

    public function register()
    {

        $this->app->bind(Logger::class, function () {
            $config = $this->getMailConfig();
            $transport = (new Swift_SmtpTransport)
                ->setHost($config['host'] ?? '')
                ->setPort($config['port'] ?? 587)
                ->setEncryption($config['encryption'] ?? null) // Cannot ?? 'tls' because null is a valid option
                ->setUsername($config['username'] ?? '')
                ->setPassword($config['password'] ?? '');
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

    private function getMailConfig(): array
    {
        // L5.x use whole mail config file, L7.x use the default config
        return config('mail.driver') ? config('mail') : config('mail.mailers.' . config('mail.default'));
    }
}
