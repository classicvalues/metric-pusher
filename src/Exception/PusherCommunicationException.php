<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Exception;

use Exception;

/**
 * Will be thrown if error occurred during connecting or sending metrics to storage
 */
class PusherCommunicationException extends Exception
{

}