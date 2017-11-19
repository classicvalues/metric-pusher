<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Pusher;

use Calltouch\MetricPusher\Exception\PusherMissedDependencyException;
use Calltouch\MetricPusher\Exception\PusherCommunicationException;
use Calltouch\MetricPusher\Pusher\InfluxDb\MetricConverter;

/**
 * Pusher for InfluxDB HTTP API
 *
 * @link https://docs.influxdata.com/influxdb/v1.3/guides/writing_data/
 */
class InfluxDbHttpApiPusher implements PusherInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $database;

    /**
     * @var array
     */
    private $params;

    /**
     * @var resource
     */
    private $curlChannel;

    /**
     * @var MetricConverter
     */
    private $metricConverter;

    /**
     * @param string $url
     * @param string $database
     * @param array  $params
     */
    public function __construct(string $url, string $database, array $params = [])
    {
        $this->url      = $url;
        $this->database = $database;
        $this->params   = $params;

        $this->metricConverter = new MetricConverter();
    }

    /**
     * @throws PusherMissedDependencyException
     */
    private function checkDependency(): void
    {
        if(!extension_loaded('curl')) {
            throw new PusherMissedDependencyException(sprintf('Curl extension required for %s', InfluxDbHttpApiPusher::class));
        }
    }

    /**
     * Make an initialization before sending metrics
     *
     * @return mixed
     */
    public function init(): void
    {
        $this->checkDependency();

        $url =  $this->url . '/write?db=' . $this->database;
        $url .= isset($this->params['user']) ? '&u=' . $this->params['user']: '';
        $url .= isset($this->params['password']) ? '&p=' . $this->params['password']: '';

        $this->curlChannel = curl_init();
        curl_setopt($this->curlChannel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlChannel, CURLOPT_POST, true);
        curl_setopt($this->curlChannel, CURLOPT_URL, $url);
    }

    /**
     * {@inheritdoc}
     */
    public function sendMetrics(array $metrics): void
    {
        $body = $this->metricConverter->convertMetricsToLineProtocol($metrics);

        curl_setopt($this->curlChannel, CURLOPT_POSTFIELDS, $body);

        $body = curl_exec($this->curlChannel);
        $info = curl_getinfo($this->curlChannel);

        $error = curl_error($this->curlChannel);
        if ($error) {
            throw new PusherCommunicationException(sprintf('Curl error: %s', $error));
        }

        if ($info['http_code'] !== 204) {
            throw new PusherCommunicationException(sprintf('Response code: %s, body: %s', $info['http_code'], $body));
        }
    }

    /**
     * Make an uninitialization after all metrics have been sent
     *
     * @return mixed
     */
    public function uninit(): void
    {
        curl_close($this->curlChannel);
        $this->curlChannel = null;
    }
}