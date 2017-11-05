<?php

namespace San4io\RequestLogger\Logger\ContextFormatters;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use San4io\RequestLogger\Contracts\ContextFormatterContract;

class RequestParamsFormatter implements ContextFormatterContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return array
     */
    public function format(Request $request, Response $response)
    {
        return $request->except(
            config('request-logger.param_exceptions')
        );
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'request_params';
    }
}