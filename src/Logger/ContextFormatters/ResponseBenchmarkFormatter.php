<?php

namespace San4io\RequestLogger\Logger\ContextFormatters;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use San4io\RequestLogger\Contracts\ContextFormatterContract;
use San4io\RequestLogger\Services\BenchmarkService;

class ResponseBenchmarkFormatter implements ContextFormatterContract
{
    /**
     * @var BenchmarkService
     */
    protected $benchmarkService;

    /**
     * BenchmarkFormatter constructor.
     * @param BenchmarkService $benchmarkService
     */
    public function __construct(BenchmarkService $benchmarkService)
    {
        $this->benchmarkService = $benchmarkService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return string
     */
    public function format(Request $request, Response $response)
    {
        return $this->benchmarkService->result();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'response_ms';
    }
}