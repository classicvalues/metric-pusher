<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\MetricData;

interface MetricInterface
{
    /**
     * @return string
     */
    public function getMetricName(): string;

    /**
     * @return DataCollection
     */
    public function getData(): DataCollection;

    /**
     * @return TagCollection
     */
    public function getTags(): TagCollection;
}