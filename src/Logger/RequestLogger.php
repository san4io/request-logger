<?php

namespace San4io\RequestLogger\Logger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class RequestLogger
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LogContextFormatter
     */
    protected $logContextFormatter;

    /**
     * RequestLogger constructor.
     * @param LoggerInterface $logger
     * @param LogContextFormatter $logContextFormatter
     */
    public function __construct(LoggerInterface $logger, LogContextFormatter $logContextFormatter)
    {
        $this->logger = $logger;
        $this->logContextFormatter = $logContextFormatter;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function log(Request $request, Response $response)
    {
        $context = $this->logContextFormatter->format($request, $response);

        $this->logger->log(
            Logger::INFO,
            $request->getMethod() . ' ' . $request->getUri(),
            $context
        );
    }
}