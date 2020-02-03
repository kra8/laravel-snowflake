<?php
namespace Kra8\Snowflake;

use Exception;

class Snowflake
{
    const TIMESTAMP_LEFT_SHIFT      = 22;

    const DATACENTER_ID_LEFT_SHIFT  = 17;

    const WORKER_ID_LEFT_SHIFT      = 12;

    private $epoch;

    private $lastTimestamp;

    private $datacenterId;

    private $sequence;

    private $workerId;

    private $timeout = 1000;

    private $startTimeout = null;

    /**
     * @throws Exception
     */
    public function __construct($timestamp, $workerId = 1, $datacenterId = 1)
    {
        $this->epoch = $timestamp * 1000;

        $this->workerId         = $workerId;
        $this->datacenterId     = $datacenterId;
        $this->lastTimestamp    = $this->epoch;
        $this->sequence         = 0;
    }

    /**
     * Generate the 64bit unique id.
     *
     * @return integer
     *
     * @throws Exception
     */
    public function next()
    {
        $timestamp = $this->timestamp();

        if ($timestamp < static::$lastTimestamp) {
            if (!$this->startTimeout) {
                $this->startTimeout = $timestamp;
            }

            if (($timestamp - $this->startTimeout) < $this->timeout) {
                usleep(static::$lastTimestamp - $timestamp);
                return $this->next();
            }

            $errorLog = "[Timeout({$this->timeout})] Couldn't generation snowflake id, os time is backwards. [last timestamp:" . static::$lastTimestamp ."]";
            throw new Exception($errorLog);
        }

        if ($timestamp === $this->lastTimestamp) {
            $this->sequence = $this->sequence + 1;
            if ($this->sequence > 4095) {
                usleep(1);
                $timestamp = $this->timestamp();
                $this->sequence = 0;
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;
        $this->startTimeout = null;

        return (($timestamp - $this->epoch) << self::TIMESTAMP_LEFT_SHIFT)
        | ($this->datacenterId << self::DATACENTER_ID_LEFT_SHIFT)
        | ($this->workerId << self::WORKER_ID_LEFT_SHIFT)
        | $this->sequence;
    }

    /**
     * Return the now unixtime.
     *
     * @return integer
     */
    protected function timestamp()
    {
        return round(microtime(true) * 1000);
    }
}
