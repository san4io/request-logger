<?php

namespace San4io\RequestLogger\Logger\ContextFormatters;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use San4io\RequestLogger\Contracts\ContextFormatterContract;

class RequestUriFormatter implements ContextFormatterContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return string
     */
    public function format(Request $request, Response $response)
    {
        return $request->getUri();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'request_uri';
    }
}