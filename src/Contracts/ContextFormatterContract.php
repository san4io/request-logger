<?php

namespace San4io\RequestLogger\Contracts;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ContextFormatterContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return mixed
     */
    public function format(Request $request, Response $response);

    /**
     * @return string
     */
    public function name(): string;
}