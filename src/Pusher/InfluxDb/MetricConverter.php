<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Pusher\InfluxDb;

use Calltouch\MetricPusher\MetricData\Metric;
use Calltouch\MetricPusher\MetricData\MetricInterface;
use Calltouch\MetricPusher\MetricData\TimeMetricInterface;

class MetricConverter
{
    /**
     * @var LineProtocolEncoder
     */
    private $lineProtocolEncoder;

    /**
     * MetricConverter constructor.
     */
    public function __construct()
    {
        $this->lineProtocolEncoder = new LineProtocolEncoder();
    }

    /**
     * Creates Point object from metric object
     *
     * @param MetricInterface $metric
     *
     * @return string
     */
    private function createPoint(MetricInterface $metric): string
    {
        $pointData = $this->lineProtocolEncoder->escapeName($metric->getMetricName());

        $tags = [];
        foreach ($metric->getTags()->all() as $tag) {
            $tagKey   = $this->lineProtocolEncoder->escapeName($tag->getName());
            $tagValue = $this->lineProtocolEncoder->escapeName($tag->getValue());

            $tags[] = sprintf('%s=%s', $tagKey, $tagValue);
        }

        if ($tags) {
            $pointData .= ',' . implode(',', $tags);
        }

        $fields = [];
        foreach ($metric->getData()->all() as $data) {
            $fieldKey   = $this->lineProtocolEncoder->escapeName($data->getName());
            $fieldValue = $this->lineProtocolEncoder->translateFieldValue($data->getValue());

            $fields[]   = sprintf('%s=%s', $fieldKey, $fieldValue);
        }
        $pointData .= ' ' . implode(',', $fields);

        if ($metric instanceof TimeMetricInterface) {
            // time in nanoseconds
            $pointData .= ' ' . $metric->getTime()->format('Uu000');
        }

        return $pointData;
    }

    /**
     * @param Metric[] $metrics
     *
     * @return string
     */
    public function convertMetricsToLineProtocol($metrics): string
    {
        $points = [];
        /** @var MetricInterface $metric */
        foreach ($metrics as $metric) {
            $points[] = $this->createPoint($metric);
        }
        $data = implode("\n", $points);

        return $data;
    }
}