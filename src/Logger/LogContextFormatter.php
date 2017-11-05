<?php

namespace San4io\RequestLogger\Logger;

use Illuminate\Support\Collection;
use San4io\RequestLogger\Contracts\ContextFormatterContract;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogContextFormatter implements ContextFormatterContract
{
    /**
     * @var array
     */
    protected $context = [];

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * LogContextFormatter constructor.
     */
    public function __construct()
    {
        $this->collection = new Collection();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array
     */
    public function format(Request $request, Response $response)
    {
        $this->collection->each(
            function (ContextFormatterContract $contextFormatterContract) use ($request, $response) {
                $this->context[$contextFormatterContract->name()] = $contextFormatterContract->format($request, $response);
            }
        );

        return $this->context;
    }

    /**
     * @param ContextFormatterContract $newFormatter
     */
    public function addContextFormatter(ContextFormatterContract $newFormatter)
    {
        $this->validate($newFormatter);
        $this->collection->push($newFormatter);
    }

    /**
     * @param ContextFormatterContract $newFormatter
     * @throws \Exception
     */
    protected function validate(ContextFormatterContract $newFormatter): void
    {
        $exists = $this->collection->first(function (ContextFormatterContract $formatter) use ($newFormatter) {
            return $formatter->name() == $newFormatter->name();
        });

        if ($exists) {
            throw new \Exception('Context formatter with name "' . $exists->name() . '" already exits');
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'log-context-formatter';
    }

}