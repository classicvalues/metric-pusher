<?php

namespace Calltouch\MetricPusher\MetricData;

class TagCollection
{
    /**
     * @var Tag[]
     */
    private $tags = [];

    public function add(Tag $tag)
    {
        $this->tags[$tag->getName()] = $tag;
    }

    /**
     * @return Tag[]
     */
    public function all(): array
    {
        $tags = array_values($this->tags);

        return $tags;
    }
}