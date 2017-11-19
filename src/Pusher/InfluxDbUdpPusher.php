<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Pusher;

use Calltouch\MetricPusher\Exception\PusherMissedDependencyException;
use Calltouch\MetricPusher\Exception\PusherCommunicationException;
use Calltouch\MetricPusher\MetricData\Metric;
use Calltouch\MetricPusher\Pusher\InfluxDb\MetricConverter;

class InfluxDbUdpPusher implements PusherInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var array
     */
    private $params;

    /**
     * @var MetricConverter
     */
    private $metricConverter;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @param string $host
     * @param int    $port
     * @param array  $params
     */
    public function __construct(string $host, int $port, array $params = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->params = $params;

        $this->metricConverter = new MetricConverter();
    }

    /**
     * Make an initialization before sending metrics
     *
     * @throws PusherMissedDependencyException
     *
     * @return mixed
     */
    public function init(): void
    {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    /**
     * @param Metric[] $metrics
     *
     * @throws PusherCommunicationException
     */
    public function sendMetrics(array $metrics): void
    {
        $data = $this->metricConverter->convertMetricsToLineProtocol($metrics);

        $errorMessage = null;
        set_error_handler(function (int $errno, string $err) use (&$errorMessage) {
            $errorMessage = $err;
        });

        socket_sendto($this->socket, $data, strlen($data), 0, $this->host, $this->port);

        restore_error_handler();

        if ($errorMessage) {
            $msg = sprintf("Error occurred host: %s, port: %s, error: %s", $this->host, $this->port, $errorMessage);
            throw new PusherCommunicationException($msg);
        }
    }

    /**
     * Make an uninitialization after all metrics have been sent
     *
     * @return mixed
     */
    public function uninit(): void
    {
        socket_close($this->socket);
        $this->socket = null;
    }
}