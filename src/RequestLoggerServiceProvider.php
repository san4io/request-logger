<?php

namespace San4io\RequestLogger;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use San4io\RequestLogger\Logger\ContextFormatters\ResponseBenchmarkFormatter;
use San4io\RequestLogger\Logger\ContextFormatters\RequestHeadersFormatter;
use San4io\RequestLogger\Logger\ContextFormatters\RequestMethodFormatter;
use San4io\RequestLogger\Logger\ContextFormatters\RequestParamsFormatter;
use San4io\RequestLogger\Logger\ContextFormatters\RequestUriFormatter;
use San4io\RequestLogger\Logger\ContextFormatters\ResponseContentFormatter;
use San4io\RequestLogger\Logger\LogContextFormatter;
use San4io\RequestLogger\Logger\RequestLogger;
use San4io\RequestLogger\Services\BenchmarkService;

class RequestLoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes([
            __DIR__ . '/../../../resources/config/request-logger.php' => config_path('request-logger.php')
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/Config/request-logger.php', 'request-logger'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLogger();
        $this->registerAppBenchmarkFormatter();
        $this->registerLogContextFormatter();
    }

    /**
     *
     */
    protected function registerLogger()
    {
        $this->app->bind('app.request.logger', function () {
            $logger = new Logger('request-logger');

            $handler = new RotatingFileHandler(
                storage_path(config('request-logger.storage_path'))
            );
            $handler->setFormatter(new LogstashFormatter(
                env('APP_NAME'),
                null,
                null,
                null
            ));

            $logger->pushHandler($handler);

            return $logger;
        });

        $this->app->bind(RequestLogger::class, function (Application $app) {
            return new RequestLogger(
                $app->make('app.request.logger'),
                $app->make(LogContextFormatter::class)
            );
        });
    }

    /**
     *
     */
    protected function registerAppBenchmarkFormatter()
    {
        $this->app->singleton('app.services.benchmark.application', function (Application $app) {
            return new BenchmarkService('application');
        });

        $this->app->bind(ResponseBenchmarkFormatter::class, function (Application $app) {
            return new ResponseBenchmarkFormatter(
                $app->make('app.services.benchmark.application')
            );
        });
    }

    /**
     *
     */
    protected function registerLogContextFormatter(): void
    {
        $this->app->bind(LogContextFormatter::class, function (Application $app) {
            $contextFormatter = new LogContextFormatter();

            $contextFormatter->addContextFormatter($app->make(RequestMethodFormatter::class));
            $contextFormatter->addContextFormatter($app->make(RequestUriFormatter::class));
            $contextFormatter->addContextFormatter($app->make(RequestParamsFormatter::class));
            $contextFormatter->addContextFormatter($app->make(RequestHeadersFormatter::class));
            $contextFormatter->addContextFormatter($app->make(ResponseContentFormatter::class));
            $contextFormatter->addContextFormatter($app->make(ResponseBenchmarkFormatter::class));

            return $contextFormatter;
        });
    }
}
