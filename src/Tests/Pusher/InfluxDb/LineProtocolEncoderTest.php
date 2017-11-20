<?php
declare(strict_types = 1);

namespace Calltouch\MetricPusher\Tests\Pusher\InfluxDb;

use Calltouch\MetricPusher\Pusher\InfluxDb\LineProtocolEncoder;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class LineProtocolEncoderTest extends TestCase
{
    /**
     * @var LineProtocolEncoder
     */
    private $lineProtocolEncoder;

    protected function setUp()
    {
        parent::setUp();

        $this->lineProtocolEncoder = new LineProtocolEncoder();
    }

    public function providerSuccessTranslateValue()
    {
        return [
            [1, '1i'], // integer
            [-2, '-2i'], // negative integer
            [12.111, '12.1110000000'], // float
            [2.0, '2.0000000000'], // another float
            [-2.1111, '-2.1111000000'], // negative  float
            [true, 't'], // boolean true
            [false, 'f'], // boolean false
            ['xxxx', '"xxxx"'], // string
            ['xx"xx', '"xx\\"xx"'], // string with double quotes
        ];
    }

    /**
     * @param $value
     * @param $expected
     *
     * @dataProvider providerSuccessTranslateValue
     */
    public function testSuccessTranslateValue($value, $expected)
    {
        $result = $this->lineProtocolEncoder->translateFieldValue($value);

        self::assertSame($expected, $result);
    }

    public function providerFailedTranslateValue()
    {
        return [
            [null], // null
            [new \stdClass()], // object
            [function(){}], // callable
            [[]], // array
            [new \ArrayIterator([])], // iterable
            [tmpfile()], // resource
        ];
    }

    /**
     * @param $value
     *
     * @dataProvider providerFailedTranslateValue
     */
    public function testFailedTranslateValue($value)
    {
        self::expectException(InvalidArgumentException::class);

        $this->lineProtocolEncoder->translateFieldValue($value);
    }

    public function providerSuccessEscaping()
    {
        return [
            ['123qwertyuiop[]', '123qwertyuiop[]'], // simple string
            ['xx xx', 'xx\\ xx'], // special symbol " "
            ['xx,xx', 'xx\\,xx'], // special symbol ","
            ['xx=xx', 'xx\\=xx'], // special symbol "="
            ['xx"x"x', 'xx"x"x'], // non special symbol double quotes
        ];
    }

    /**
     * @param $value
     * @param $expected
     *
     * @dataProvider providerSuccessEscaping
     */
    public function testSuccessEscaping($value, $expected)
    {
        $result = $this->lineProtocolEncoder->escapeName($value);

        self::assertSame($expected, $result);
    }


}