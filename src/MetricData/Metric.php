<?php

namespace Calltouch\MetricPusher\MetricData;

class Metric implements MetricInterface
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
     * @param string         $metricName
     * @param DataCollection $data
     * @param TagCollection  $tags
     */
    public function __construct(string $metricName, DataCollection $data, TagCollection $tags)
    {
        $this->metricName = $metricName;
        $this->data       = $data;
        $this->tags       = $tags;
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
}