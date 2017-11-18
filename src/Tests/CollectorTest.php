<?php

namespace Calltouch\MetricPusher\Tests;

use Calltouch\MetricPusher\Collector;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Calltouch\MetricPusher\Pusher\PusherInterface;
use Calltouch\MetricPusher\MetricData\Metric;
use Calltouch\MetricPusher\MetricData\DataCollection;
use Calltouch\MetricPusher\MetricData\TagCollection;
use Calltouch\MetricPusher\MetricData\Data;
use Calltouch\MetricPusher\MetricData\Tag;

class CollectorTest extends TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject | PusherInterface
     */
    private $pusherMock;

    protected function setUp()
    {
        parent::setUp();

        $this->pusherMock = self::getMockBuilder(PusherInterface::class)->getMock();
    }

    public function testS()
    {
        $collector = new Collector($this->pusherMock);

        $metricName = 'm1';

        $data = new DataCollection();
        $data->add(new Data('dataName1', -11));
        $data->add(new Data('dataName2', 2.1));

        $tags= new TagCollection();
        $tags->add(new Tag('tagName1', 'tagValue1'));
        $tags->add(new Tag('tagName2', 'tagValue2'));

        $expectedMetric = new Metric($metricName, $data, $tags);
        $this->pusherMock->expects(self::once())->method('sendMetrics')->with([$expectedMetric]);

        $collector->sendMetric($expectedMetric);
    }
}
