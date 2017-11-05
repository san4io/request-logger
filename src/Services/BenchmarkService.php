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
    protected $startTime;

    /**
     * @var
     */
    protected $endTime;

    /**
     * @var string
     */
    protected $name;

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

        if (!$this->startTime) {
            $this->setDefaultStartTime();
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


    /**
     * Get StartTime if such exists
     */
    protected function setDefaultStartTime()
    {
        if (defined('LARAVEL_START')) {
            $this->startTime = LARAVEL_START;
        } else {
            $this->startTime = microtime(true);
        }
    }

}