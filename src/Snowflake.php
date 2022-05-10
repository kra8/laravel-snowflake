<?php declare(strict_types=1);

namespace Kra8\Snowflake;

use Exception;

class Snowflake
{
    protected const DEFAULT_EPOCH_DATETIME = '2021-02-02 00:00:00';

    protected const ID_BITS = 63;

    protected const TIMESTAMP_BITS = 41;

    protected const WORKER_ID_BITS = 5;

    protected const DATACENTER_ID_BITS = 5;

    protected const SEQUENCE_BITS = 12;

    protected const TIMEOUT = 1000;

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

        // もし、今回生成したタイムスタンプが、前回生成したタイムスタンプより過去のものなら、待機する
        // そもそもこのような状況が起こるのかは不明なので、不要かもしれない。
        if ($timestamp < $this->lastTimestamp) {
            $waitTime = $this->lastTimestamp - $timestamp;

            // 待機時間がtimeout以上なら、例外を投げる
            if ($waitTime > self::TIMEOUT) {
                $errorLog = "[Timeout({self::TIMEOUT})] Couldn't generation snowflake id, os time is backwards. [last timestamp:" . $this->lastTimestamp ."]";
                throw new Exception($errorLog);
            }

            // 待機時間がtimeout以下なら、待機して再生成
            usleep($this->lastTimestamp - $timestamp);
            return $this->next();
        }

        // タイムスタンプが同じなら、シーケンスをインクリメント
        if ($timestamp === $this->lastTimestamp) {
            $this->sequence = $this->sequence + 1;
            // シーケンス番号が最大値を超えたら、待機して再生成
            if ($this->sequence > 4095) {
                usleep(1);
                $this->sequence = 0;
                return $this->next();
            }
        } else {
            $this->sequence = mt_rand(0, 4095);
        }

        $this->lastTimestamp = $timestamp;
        return $this->toSnowflakeId($timestamp - $this->epoch, $this->sequence);
    }

    public function toSnowflakeId(int $currentTime, int $sequence)
    {
        $workerIdLeftShift = self::SEQUENCE_BITS;
        $datacenterIdLeftShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampLeftShift = self::DATACENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        return ($currentTime << $timestampLeftShift)
            | ($this->datacenterId << $datacenterIdLeftShift)
            | ($this->workerId << $workerIdLeftShift)
            | ($sequence);
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

    /**
     * Create 53bit Id.
     * timestamp_bits(41) + sequence_bits(12)
     *
     * @return int
     */
    public function short(): int
    {
        $parsed = $this->parse($this->next());
        return $parsed['timestamp'] << 12 | $parsed['sequence'];
    }

    /**
     * Return the now unixtime.
     *
     * @return int
     */
    public function timestamp(): int
    {
        return floor(microtime(true) * 1000) | 0;
    }
}
