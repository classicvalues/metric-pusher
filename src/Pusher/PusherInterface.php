<?php

namespace Calltouch\MetricPusher\Pusher;

use Calltouch\MetricPusher\Exception\SendMetricsException;
use Calltouch\MetricPusher\MetricData\MetricInterface;

interface PusherInterface
{
    /**
     * @param MetricInterface[] $metrics
     *
     * @throws SendMetricsException
     */
    public function sendMetrics(array $metrics): void;
}