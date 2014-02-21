<?php

use DataSift\TestBundle\Task\DummyTask;
use DataSift\TestBundle\Daemon\Daemon;
use \Zend\Config\Reader\Ini;
use \DataSift\TestBundle\Log\Logger\FileLogger;
use \DataSift\TestBundle\Server\Redis\RedisServer;
use \DataSift\TestBundle\Task\RedisTask;

require 'autoloader.php';
require 'vendor/autoload.php';

date_default_timezone_set('Europe/London');

$configIni = new Ini();
$config = $configIni->fromFile('app/config/config.ini');

$logger = new FileLogger($config['production']['path_log_file']);
$redis = new Redis();
$redis->connect($config['production']['server_adress']);

$daemon = new Daemon($logger, $config['production']);
$daemon->addTask(new DummyTask());
$daemon->addTask(new RedisTask($redis));
$daemon->run(new RedisServer($redis, new SplObjectStorage()));
