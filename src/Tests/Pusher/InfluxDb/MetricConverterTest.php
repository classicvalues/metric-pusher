<?php
declare(strict_types = 1);

namespace Calltouch\MetricPusher\Tests\Pusher\InfluxDb;

use Calltouch\MetricPusher\MetricData\{
    Metric, Tag, TagCollection, Data, DataCollection, TimeMetric
};
use Calltouch\MetricPusher\Pusher\InfluxDb\MetricConverter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use DateTime;

class MetricConverterTest extends TestCase
{
    /**
     * @var MetricConverter
     */
    private $metricConverter;

    protected function setUp()
    {
        parent::setUp();

        $this->metricConverter = new MetricConverter();
    }

    public function providerSuccessConvertMetricsToLineProtocol()
    {
        return [
            // one metric, one value
            [
                [
                    ['metric' => 'm1', 'tags' => [], 'data' => ['dk1' => 1]],
                ],
                "m1 dk1=1i",
            ],
            // one metric, one value, two value
            [
                [
                    ['metric' => 'm1', 'tags' => [], 'data' => ['dk1' => 1, 'dk2' => 2]]
                ],
                "m1 dk1=1i,dk2=2i",
            ],
            // one metric, one value, one tag
            [
                [
                    ['metric' => 'm1', 'tags' => ['tk1' => 'tv1'], 'data' => ['dk1' => 1]]
                ],
                "m1,tk1=tv1 dk1=1i",
            ],
            // one metric, one value, two tag
            [
                [
                    ['metric' => 'm1', 'tags' => ['tk1' => 'tv1','tk2' => 'tv2'], 'data' => ['dk1' => 1]]
                ],
                "m1,tk1=tv1,tk2=tv2 dk1=1i",
            ],
            // zero metrics
            [
                [],
                "",
            ],
            // two metrics
            [
                [
                    ['metric' => 'm1', 'tags' => [], 'data' => ['dk1' => 1]],
                    ['metric' => 'm1', 'tags' => [], 'data' => ['dk1' => 2]],
                ],
                "m1 dk1=1i\nm1 dk1=2i",
            ],
        ];
    }

    /**
     * @param array  $metricsData
     * @param string $expected
     *
     * @dataProvider providerSuccessConvertMetricsToLineProtocol
     */
    public function testSuccessConvertMetricsToLineProtocol(array $metricsData, string $expected)
    {
        $metrics = [];
        foreach ($metricsData as $metricData) {
            $tags = new TagCollection;
            foreach ($metricData['tags'] as $key => $value) {
                $tags->add(new Tag($key, $value));
            }

            $data = new DataCollection;
            foreach ($metricData['data'] as $key => $value) {
                $data->add(new Data($key, $value));
            }

            $metrics[] = new Metric($metricData['metric'], $data, $tags);
        }

        $result = $this->metricConverter->convertMetricsToLineProtocol($metrics);

        self::assertSame($expected, $result);
    }

    public function testSuccessConvertTimeMetricToLineProtocol()
    {
        $tags = new TagCollection;
        $tags->add(new Tag('tk1', 'tv1'));

        $data = new DataCollection;
        $data->add(new Data('dk1', 1));

        $metric = new TimeMetric('m1', $data, $tags, new DateTime('2017-01-03 04:05:05.11111'));
        $expected = "m1,tk1=tv1 dk1=1i 1483416305111110000";

        $result = $this->metricConverter->convertMetricsToLineProtocol([$metric]);

        self::assertSame($expected, $result);
    }

    public function testFailedWithoutMetric()
    {
        $metric = new Metric('m1', new DataCollection, new TagCollection);

        self::expectException(InvalidArgumentException::class);

        $this->metricConverter->convertMetricsToLineProtocol([$metric]);
    }
}