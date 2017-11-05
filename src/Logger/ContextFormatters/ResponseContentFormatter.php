<?php

namespace San4io\RequestLogger\Logger\ContextFormatters;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use San4io\RequestLogger\Contracts\ContextFormatterContract;

class ResponseContentFormatter implements ContextFormatterContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return string
     */
    public function format(Request $request, Response $response)
    {
        return $response->getContent();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'response_content';
    }
}