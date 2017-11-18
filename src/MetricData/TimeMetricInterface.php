<?php

namespace Calltouch\MetricPusher\MetricData;

use DateTimeInterface;

interface TimeMetricInterface
{
    /**
     * @return DateTimeInterface
     */
    public function getTime();
}