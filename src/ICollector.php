<?php

namespace Calltouch\MetricPusher;

use Calltouch\MetricPusher\MetricData\MetricInterface;

interface ICollector
{
    /**
     * @param MetricInterface $metric
     */
    public function sendMetric(MetricInterface $metric): void;
}