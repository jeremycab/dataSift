<?php

use DataSift\TestBundle\Task\DummyTask;
use DataSift\TestBundle\Daemon\Daemon;
use \Zend\Config\Reader\Ini;
use \DataSift\TestBundle\Log\Logger\FileLogger;
use \DataSift\TestBundle\Server\Socket\Server\SocketServer;
use DataSift\TestBundle\Task\ZeroMqTask;
use \DataSift\TestBundle\Server\Redis\RedisServer;
use \DataSift\TestBundle\Task\RedisTask;

require 'autoloader.php';
require 'vendor/autoload.php';

date_default_timezone_set('Europe/London');

$configIni = new Ini();
$config = $configIni->fromFile('app/config/config.ini');

$logger = new FileLogger($config['production']['path_log_file']);

$serverSocket = new SocketServer(
                $config['production']['server_adress'],
                $config['production']['server_port'],
                $config['production']['nb_clients_max'],
                $logger);

$redis = new Redis();
$redis->connect($config['production']['server_adress']);
$serverRedis = new RedisServer($redis, new SplObjectStorage());

$zero = new ZMQSocket(new ZMQContext(), ZMQ::SOCKET_REQ);

$daemon = new Daemon($logger, $config['production']);

$daemon->addServer($serverSocket);
$daemon->addServer($serverRedis);

$daemon->addTask(new DummyTask());
$daemon->addTask(new RedisTask($redis));
//$daemon->addTask(new ZeroMqTask($zero));

$daemon->run();
