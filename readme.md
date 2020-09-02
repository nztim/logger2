# Logger

Logger complements standard Laravel logging.
It logs to local files and optionally provides email alerts.

### Installation

* Register the service provider: `NZTim\Logger\LoggerServiceProvider::class,`

### Configuration

Publish the configuration file with: `php artisan vendor:publish --provider=NZTim\Logger\LoggerServiceProvider`.

* `'laravel' => true,` captures Laravel log events
* `'single' => []` list of log files to be handled as single logs (daily is default)
* `'max_daily' => 7` maximum number of daily log files to keep
* `'email.send' => false` turns sending of error emails on/off
* `'email.from' => 'sender@example.com',` email sender address
* `'email.to' => 'recipient@example.com',` email recipient

### Usage

Inject or use helper functions to log to custom log files. Files are stored in `storage/logs/custom`:

```
// Helper functions
log_info('auth', 'User login from 1.2.3.4');
log_warning('audit', 'A record was updated');
log_error('exceptions', 'Fatal exception', ['context' => 'array']);
```

Fatal errors occurring during the logging process, are stored in `storage/logs/fatal-logger-errors.log`.
For example, a message will be logged here when the system is unable to send an error notification.

### Email alerts

Emails are only triggered if email sending is turned on, `app.debug` is false and the level is at least ERROR severity.
The Laravel mail system must be configured for emails to function.

### Changelog

* v3.0: Switch to daily by default, config option is now 'single'.
* v2.0: Remove facade. Requires monolog 2.0+ (Laravel 6.0)
* v1.0: Complete rewrite using monolog handlers, only basic functionality retained. RequestInfo method removed. Do not upgrade without reviewing and updating all use of this package.
