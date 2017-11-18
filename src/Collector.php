<?php

namespace Calltouch\MetricPusher;

use Calltouch\MetricPusher\MetricData\MetricInterface;
use Calltouch\MetricPusher\Pusher\PusherInterface;

class Collector implements ICollector
{
    /**
     * @var PusherInterface
     */
    private $pusher;

    /**
     * @param PusherInterface $pusher
     */
    public function __construct(PusherInterface $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * {@inheritdoc}
     */
    public function sendMetric(MetricInterface $metric): void
    {
        $this->pusher->sendMetrics([$metric]);
    }
}