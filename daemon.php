<?php

use DataSift\TestBundle\Log\Logger\EchoLogger;
use \DataSift\TestBundle\Task\SumTask;
use DataSift\TestBundle\Bootstrap\Bootstrap;
use \Zend\Config\Reader\Ini;

require 'autoloader.php';
require 'vendor/autoload.php';

$configIni = new Ini();
$config = $configIni->fromFile('app/config/config.ini');

$bootstrap = new Bootstrap(new EchoLogger(), $config['production']);
$bootstrap->addTask(new SumTask());
$bootstrap->run();
