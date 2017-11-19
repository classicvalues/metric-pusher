<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\MetricData;

use DateTimeInterface;

class TimeMetric implements MetricInterface, TimeMetricInterface
{
    /**
     * @var string
     */
    private $metricName;

    /**
     * @var DataCollection
     */
    private $data;

    /**
     * @var TagCollection
     */
    private $tags;

    /**
     * @var DateTimeInterface
     */
    private $time;

    /**
     * @param string            $metricName
     * @param DataCollection    $data
     * @param TagCollection     $tags
     * @param DateTimeInterface $time
     */
    public function __construct(string $metricName, DataCollection $data, TagCollection $tags, DateTimeInterface $time)
    {
        $this->metricName = $metricName;
        $this->data       = $data;
        $this->tags       = $tags;
        $this->time       = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetricName(): string
    {
        return $this->metricName;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): DataCollection
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags(): TagCollection
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime(): DateTimeInterface
    {
        return $this->time;
    }
}