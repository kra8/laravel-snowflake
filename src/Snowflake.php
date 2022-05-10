<?php

declare(strict_types=1);

namespace Kra8\Snowflake;

class Snowflake
{
    protected const DEFAULT_EPOCH_DATETIME = '2021-02-02 00:00:00';

    protected const ID_BITS = 63;

    protected const TIMESTAMP_BITS = 41;

    protected const WORKER_ID_BITS = 5;

    protected const DATACENTER_ID_BITS = 5;

    protected const SEQUENCE_BITS = 12;

    protected const TIMEOUT = 1000;

    protected const MAX_SEQUENCE = 4095;

    /**
     * The epoch time.
     *
     * @var int
     */
    protected $epoch;

    /**
     * The last timestamp.
     *
     * @var int
     */
    private $lastTimestamp;

    /**
     * The sequence number.
     *
     * @var int
     */
    private $sequence = 0;

    /**
     * The datacenter id.
     *
     * @var int
     */
    private $datacenterId;

    /**
     * The worker id.
     *
     * @var int
     */
    private $workerId;

    /**
     * Create a new Snowflake instance.
     */
    public function __construct(int $timestamp = null, int $workerId = 1, int $datacenterId = 1)
    {
        if ($timestamp === null) {
            $timestamp = strtotime(self::DEFAULT_EPOCH_DATETIME);
        }

        $this->epoch = $timestamp * 1000;
        $this->workerId = $workerId;
        $this->datacenterId = $datacenterId;
        $this->lastTimestamp = $this->epoch;
    }

    public function makeSequenceId(int $currentTime, int $max = self::MAX_SEQUENCE): int
    {
        if ($this->lastTimestamp === $currentTime) {
            $this->sequence = $this->sequence + 1;
            return $this->sequence;
        }

        $this->sequence = mt_rand(0, $max);
        $this->lastTimestamp = $currentTime;
        return $this->sequence;
    }

    /**
     * Generate the 64bit unique id.
     *
     * @return int
     */
    public function id(): int
    {
        $currentTime = $this->timestamp();
        while (($sequenceId = $this->makeSequenceId($currentTime)) > self::MAX_SEQUENCE) {
            usleep(1);
            $currentTime = $this->timestamp();
        }

        $this->lastTimestamp = $currentTime;
        return $this->toSnowflakeId($currentTime - $this->epoch, $sequenceId);
    }

    /**
     * Generate the 64bit unique id.
     *
     * @return int
     */
    public function next(): int
    {
        return $this->id();
    }

    /**
     * Create 53bit Id.
     * timestamp_bits(41) + sequence_bits(12)
     *
     * @return int
     */
    public function short(): int
    {
        $currentTime = $this->timestamp();
        while (($sequenceId = $this->makeSequenceId($currentTime)) > self::MAX_SEQUENCE) {
            usleep(1);
            $currentTime = $this->timestamp();
        }

        $this->lastTimestamp = $currentTime;
        return $this->toShortflakeId($currentTime - $this->epoch, $sequenceId);
    }

    public function toShortflakeId(int $currentTime, int $sequenceId)
    {
        return ($currentTime << self::SEQUENCE_BITS) | ($sequenceId);
    }

    public function toSnowflakeId(int $currentTime, int $sequenceId)
    {
        $workerIdLeftShift = self::SEQUENCE_BITS;
        $datacenterIdLeftShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampLeftShift = self::DATACENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        return ($currentTime << $timestampLeftShift)
            | ($this->datacenterId << $datacenterIdLeftShift)
            | ($this->workerId << $workerIdLeftShift)
            | ($sequenceId);
    }

    /**
     * Return the now unixtime.
     *
     * @return int
     */
    public function timestamp(): int
    {
        return (int) floor(microtime(true) * 1000);
    }

    public function parse(int $id): array
    {
        $id = decbin($id);

        $datacenterIdLeftShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampLeftShift = self::DATACENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        $binaryTimestamp = substr($id, 0, -$timestampLeftShift);
        $binarySequence = substr($id, -self::SEQUENCE_BITS);
        $binaryWorkerId = substr($id, -$datacenterIdLeftShift, self::WORKER_ID_BITS);
        $binaryDatacenterId = substr($id, -$timestampLeftShift, self::DATACENTER_ID_BITS);
        $timestamp = bindec($binaryTimestamp);
        $datetime = date('Y-m-d H:i:s', ((int) (($timestamp + $this->epoch) / 1000) | 0));

        return [
            'binary_length' => strlen($id),
            'binary' => $id,
            'binary_timestamp' => $binaryTimestamp,
            'binary_sequence' => $binarySequence,
            'binary_worker_id' => $binaryWorkerId,
            'binary_datacenter_id' => $binaryDatacenterId,
            'timestamp' => $timestamp,
            'sequence' => bindec($binarySequence),
            'worker_id' => bindec($binaryWorkerId),
            'datacenter_id' => bindec($binaryDatacenterId),
            'epoch' => $this->epoch,
            'datetime' => $datetime,
        ];
    }
}
