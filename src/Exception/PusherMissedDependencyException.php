<?php
declare(strict_types=1);

namespace Calltouch\MetricPusher\Exception;

use Exception;

/**
 * Will be thrown if a dependency for pusher is missed
 */
class PusherMissedDependencyException extends Exception
{

}