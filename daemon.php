<?php

use DataSift\TestBundle\Log\Logger\EchoLogger;
use \DataSift\TestBundle\Task\SumTask;
use DataSift\TestBundle\Daemon\Daemon;
use \Zend\Config\Reader\Ini;

require 'autoloader.php';
require 'vendor/autoload.php';

$configIni = new Ini();
$config = $configIni->fromFile('app/config/config.ini');

$daemon = new Daemon(new EchoLogger(), $config['production']);
$daemon->addTask(new SumTask());
$daemon->run();
