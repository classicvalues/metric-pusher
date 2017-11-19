<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\MetricData;

use DateTimeInterface;

interface TimeMetricInterface
{
    /**
     * @return DateTimeInterface
     */
    public function getTime();
}