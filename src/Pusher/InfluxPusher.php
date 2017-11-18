<?php

namespace Calltouch\MetricPusher\Pusher;

use Calltouch\MetricPusher\Exception\SendMetricsException;
use Calltouch\MetricPusher\MetricData\TimeMetricInterface;
use Calltouch\MetricPusher\MetricData\MetricInterface;
use InfluxDB\Database;
use InfluxDB\Point;
use InfluxDB\Exception as InfluxDBException;

/**
 * Pusher for InfluxDB
 *
 * @link https://www.influxdata.com/
 */
class InfluxPusher implements PusherInterface
{
    /**
     * @var Database
     */
    private $influxDatabase;

    /**
     * @param Database $influxDatabase
     */
    public function __construct(Database $influxDatabase)
    {
        $this->influxDatabase = $influxDatabase;
    }

    /**
     * Creates Point object from metric object
     *
     * @param MetricInterface $metric
     *
     * @return Point
     */
    private function createPoint(MetricInterface $metric): Point
    {
        $fields = [];
        foreach($metric->getData()->all() as $data) {
            $fields[$data->getName()] = $data->getValue();
        }

        $tags = [];
        foreach($metric->getTags()->all() as $tag) {
            $tags[$tag->getName()] = $tag->getValue();
        }

        $time = null;
        if ($metric instanceof TimeMetricInterface) {
            $time = $metric->getTime()->format('Uu');
        }

        $point =  new Point(
            $metric->getMetricName(),
            null,
            $tags,
            $fields,
            $time
        );

        return $point;
    }

    /**
     * {@inheritdoc}
     */
    public function sendMetrics(array $metrics): void
    {
        $points = [];
        /** @var MetricInterface $metric */
        foreach ($metrics as $metric) {
            $points[] = $this->createPoint($metric);
        }

        try {
            $result = $this->influxDatabase->writePoints($points, Database::PRECISION_MICROSECONDS);
        } catch (InfluxDBException $e) {
            throw new SendMetricsException($e->getMessage(), 0, $e);
        }

        if ($result === false) {
            throw new SendMetricsException("Writing points is failed");
        }
    }
}