<?php

namespace Calltouch\MetricPusher\MetricData;

class DataCollection
{
    /**
     * @var Data[]
     */
    private $data = [];

    public function add(Data $tag)
    {
        $this->data[$tag->getName()] = $tag;
    }

    /**
     * @return Data[]
     */
    public function all(): array
    {
        $data = array_values($this->data);

        return $data;
    }
}