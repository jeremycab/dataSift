<?php

require 'autoloader.php';
require 'vendor/autoload.php';

use Zend\Console\Prompt\Line;
use Zend\Console\Console;
use \DataSift\TestBundle\Socket\Client\SocketClient;
use \Zend\Config\Reader\Ini;

$console = Console::getInstance();

$data = Line::prompt(
    'Hi, please enter data you want to send to workers : ',
    false,
    100
);

$configIni = new Ini();
$config = $configIni->fromFile('app/config/config.ini');

$client = new SocketClient($config['production']['server_adress'], $config['production']['server_port']);
$return = $client->sendData($data);
$console->writeLine("Message received from server : '$return'");