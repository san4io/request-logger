<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17.11.5
 * Time: 13.56
 */

namespace San4io\RequestLogger\Services;

/**
 * Class TimeService
 * @package San4io\RequestLogger\Services
 */
class BenchmarkService
{
    /**
     * @var mixed
     */
    protected $startTime = LARAVEL_START;

    /**
     * @var
     */
    protected $endTime;

    /**
     * TimeService constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get result
     * @return mixed
     */
    public function result()
    {
        if (!$this->endTime) {
            $this->stop();
        }

        return $this->endTime - $this->startTime;
    }

    /**
     * Start measure
     */
    public function start()
    {
        $this->startTime = microtime(true);
    }

    /**
     * End measure
     */
    public function stop()
    {
        $this->endTime = microtime(true);
    }

}