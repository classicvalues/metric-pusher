<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher;

use Calltouch\MetricPusher\MetricData\MetricInterface;

interface CollectorInterface
{
    /**
     * @param MetricInterface[] $metrics
     */
    public function sendMetrics(array $metrics): void;
}