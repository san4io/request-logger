<?php

namespace San4io\RequestLogger\Middleware;

use Closure;
use San4io\RequestLogger\Jobs\LoggingJob;
use San4io\RequestLogger\Logger\RequestLogger;
use San4io\RequestLogger\Services\BenchmarkService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\TerminableInterface;

class RequestLoggerMiddleware implements TerminableInterface
{
    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     */
    public function terminate(Request $request, Response $response)
    {
        $this->stopBenchmark();
        $this->saveLog($request, $response);
    }

    /**
     *
     */
    protected function stopBenchmark()
    {
        /** @var BenchmarkService $application */
        $application = app('app.services.benchmark.application');
        $application->stop();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    protected function saveLog(Request $request, Response $response): void
    {
        /** @var RequestLogger $requestLogger */
        $requestLogger = app(RequestLogger::class);
        $requestLogger->log($request, $response);
    }
}
