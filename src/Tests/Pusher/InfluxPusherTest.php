<?php

namespace Calltouch\MetricPusher\Tests\Pusher;

use InfluxDB\Point;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Calltouch\MetricPusher\Pusher\InfluxPusher;
use InfluxDB\Database as InfluxDBDatabase;
use InfluxDB\Exception as InfluxDBException;
use Calltouch\MetricPusher\MetricData\Metric;
use Calltouch\MetricPusher\MetricData\DataCollection;
use Calltouch\MetricPusher\MetricData\TagCollection;
use Calltouch\MetricPusher\MetricData\Tag;
use Calltouch\MetricPusher\MetricData\Data;
use Calltouch\MetricPusher\Exception\SendMetricsException;

class InfluxPusherTest extends TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject | InfluxDBDatabase
     */
    private $influxDBDatabaseMock;

    protected function setUp()
    {
        parent::setUp();

        $this->influxDBDatabaseMock = self::getMockBuilder(InfluxDBDatabase::class)->disableOriginalConstructor()->getMock();
    }

    public function testSendMetricsSuccessful()
    {
        $metricName = 'm1';
        $metric = new Metric($metricName, new DataCollection(), new TagCollection);

        $influxPusher = new InfluxPusher($this->influxDBDatabaseMock);

        $this->influxDBDatabaseMock->expects(self::once())->method('writePoints')->willReturn(true);

        $influxPusher->sendMetrics([$metric]);
    }

    public function testSendMetricsFailed()
    {
        $metricName = 'm1';
        $metric = new Metric($metricName, new DataCollection(), new TagCollection);

        $influxPusher = new InfluxPusher($this->influxDBDatabaseMock);

        $this->influxDBDatabaseMock->expects(self::once())->method('writePoints')->willReturn(false);

        self::expectException(SendMetricsException::class);

        $influxPusher->sendMetrics([$metric]);
    }

    public function testSendMetricsInfluxDBException()
    {
        $metricName = 'm1';
        $metric = new Metric($metricName, new DataCollection(), new TagCollection);

        $influxPusher = new InfluxPusher($this->influxDBDatabaseMock);
        $exception = new InfluxDBException("test");

        $this->influxDBDatabaseMock->expects(self::once())->method('writePoints')->willThrowException($exception);

        self::expectException(SendMetricsException::class);

        $influxPusher->sendMetrics([$metric]);
    }

    public function testSendMetricsPoints()
    {
        $metricName = 'm1';

        $dataName1 = 'dataName1';
        $dataValue1 = -11;

        $tagName1 = 'tagName1';
        $tagValue1 = 'tagValue1';

        $data = new DataCollection();
        $data->add(new Data($dataName1, $dataValue1));

        $tags= new TagCollection();
        $tags->add(new Tag($tagName1, $tagValue1));

        $expectedPoints = [
            new Point(
                $metricName,
                null,
                [$tagName1 => $tagValue1],
                [$dataName1 => $dataValue1]
            )
        ];

        $this->influxDBDatabaseMock->expects(self::once())->method('writePoints')->with($expectedPoints)->willReturn(true);

        $influxPusher = new InfluxPusher($this->influxDBDatabaseMock);

        $metric = new Metric($metricName, $data, $tags);

        $influxPusher->sendMetrics([$metric]);
    }

}