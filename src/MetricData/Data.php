<?php

namespace Calltouch\MetricPusher\MetricData;

class Data
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var float|null|bool
     */
    private $value;

    /**
     * @param string          $name
     * @param float|null|bool $value
     */
    public function __construct(string $name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}