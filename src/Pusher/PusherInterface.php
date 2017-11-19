<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Pusher;

use Calltouch\MetricPusher\Exception\PusherMissedDependencyException;
use Calltouch\MetricPusher\Exception\PusherCommunicationException;
use Calltouch\MetricPusher\MetricData\Metric;

interface PusherInterface
{
    /**
     * Make an initialization before sending metrics
     *
     * @throws PusherMissedDependencyException
     *
     * @return mixed
     */
    public function init(): void;

    /**
     * @param Metric[] $metrics
     *
     * @throws PusherCommunicationException
     */
    public function sendMetrics(array $metrics): void;

    /**
     * Make an uninitialization after all metrics have been sent
     *
     * @return mixed
     */
    public function uninit(): void;
}