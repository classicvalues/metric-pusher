<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Pusher\InfluxDb;

use InvalidArgumentException ;

/**
 * Encoder for influxdb line protocol
 *
 * @link https://docs.influxdata.com/influxdb/v1.3/write_protocols/line_protocol_tutorial/
 */
class LineProtocolEncoder
{
    /**
     * Escapes tag keys, tag values, field keys
     *
     * @param string $value
     *
     * @return string
     */
    public function escapeName(string $value)
    {
        $mapping = [
            ' ' => '\ ',
            ',' => '\,',
            '=' => '\=',
        ];

        $result  = strtr($value, $mapping);

        return $result;
    }

    /**
     * Translate and escape PHP string to line protocol string
     *
     * @param string $value
     *
     * @return string
     */
    private function translateStringValue(string $value): string
    {
        $escapedValue = str_replace('"', '\"', $value);
        return sprintf('"%s"', $escapedValue);
    }


    /**
     * Translate PHP boolean value to line protocol boolean
     *
     * @param bool $value
     *
     * @return string
     */
    private function translateBoolValue(bool $value): string
    {
        $result = $value ? 't' : 'f';
        return $result;
    }

    /**
     * Translate PHP integer value to line protocol integer
     *
     * @param int $value
     *
     * @return string
     */
    private function translateIntegerValue(int $value): string
    {
        $result = sprintf('%si', $value);
        return $result;
    }

    /**
     * Translate PHP integer float to line protocol float
     *
     * @param float $value
     *
     * @return string
     */
    private function translateFloatValue(float $value): string
    {
        $result = number_format($value, 16, '.', '');
        return $result;
    }

    /**
     * Translate field value
     *
     * @param $value
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function translateFieldValue($value): string
    {
        $valueType = gettype($value);
        switch ($valueType) {
            case 'boolean':
                $result = $this->translateBoolValue($value);
                break;
            case 'integer':
                $result = $this->translateIntegerValue($value);
                break;
            case 'double':
                $result = $this->translateFloatValue($value);
                break;
            case 'string':
                $result = $this->translateStringValue($value);
                break;
            default:
                throw new InvalidArgumentException ("Unsupported field value type: %s", $valueType);
        }

        return $result;
    }
}