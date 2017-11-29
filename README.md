# Metric pusher

[![Build Status](https://travis-ci.org/calltouch/metric-pusher.svg?branch=master)](https://travis-ci.org/calltouch/metric-pusher)

The library supports pushing to the following interfaces:
- InfluxDb via [HTTP API](https://docs.influxdata.com/influxdb/v1.3/guides/writing_data/)
- InfluxDb via [UDP plugin](https://docs.influxdata.com/influxdb/v1.3/tools/udp/)

## Installation

Using Composer:

```bash
composer require calltouch/metric-pusher
```

## Usage
### InfluxDb via HTTP API
```php

use Calltouch\MetricPusher\MetricData\{Metric, Tag, TagCollection, Data, DataCollection};
use Calltouch\MetricPusher\Pusher\InfluxDbHttpApiPusher;
use Calltouch\MetricPusher\Collector;

$url = 'http://127.0.0.1:8086';
$db = 'database1';

// optional params
$params = [
    'user' => 'user1', // influx user
    'password' => 'password', // influx password
    'timeout' => 100, // request max timeout in milliseconds
];

$pusher = new InfluxDbHttpApiPusher($url, $db, $params);

$collector = new Collector($pusher);

$tags = new TagCollection;
$tags->add(new Tag('host', gethostname()));

$data = new DataCollection;
$data->add(new Data('value', rand(0, 100)));

$metric = new Metric('metric_name1', $data, $tags);

$collector->sendMetrics([$metric]);
```
### InfluxDb via UDP plugin
```php
use Calltouch\MetricPusher\MetricData\{Metric, Tag, TagCollection, Data, DataCollection};
use Calltouch\MetricPusher\Pusher\InfluxDbUdpPusher;
use Calltouch\MetricPusher\Collector;

$host = '127.0.0.1';
$port = '8089';

$pusher = new InfluxDbUdpPusher($host, $port);

$collector = new Collector($pusher);

$tags = new TagCollection;
$tags->add(new Tag('host', gethostname()));

$data = new DataCollection;
$data->add(new Data('value', rand(0, 100)));

$metric = new Metric('metric_name2', $data, $tags);

$collector->sendMetrics([$metric]);
```