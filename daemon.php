<?php

use DataSift\TestBundle\Log\Logger\EchoLogger;
use DataSift\TestBundle\Task\DummyTask;
use DataSift\TestBundle\Daemon\Daemon;
use \Zend\Config\Reader\Ini;

require 'autoloader.php';
require 'vendor/autoload.php';

date_default_timezone_set('Europe/London');

$configIni = new Ini();
$config = $configIni->fromFile('app/config/config.ini');

$daemon = new Daemon(new EchoLogger(), $config['production']);
$daemon->addTask(new DummyTask());
$daemon->run();
