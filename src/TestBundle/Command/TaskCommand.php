<?php

require 'autoloader.php';
require 'vendor/autoload.php';

use Zend\Console\Prompt\Line;
use Zend\Console\Console;
use \DataSift\TestBundle\Socket\Client\SocketClient;

$console = Console::getInstance();

$data = Line::prompt(
    'Hi, please enter data you want to send to workers : ',
    false,
    100
);

$client = new SocketClient();
$return = $client->sendData($data);
$console->writeLine("Message received from worker manager : '$return'");