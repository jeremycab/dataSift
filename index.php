<?php

use \DataSift\TestBundle\Worker\Manager\WorkerManager;
use DataSift\TestBundle\Log\Logger\EchoLogger;
use \DataSift\TestBundle\Worker\Factory\WorkerFactory;
use \DataSift\TestBundle\Task\SumTask;
use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Thread\Event\ThreadEventManager;

require 'autoloader.php';

$workerManager = new WorkerManager(new ThreadManager(), new EchoLogger());
$threadEventManager = new ThreadEventManager(new ThreadManager());
$workerFactory = new WorkerFactory();

$threadEventManager->addEventObserver($workerManager);
$workerManager->launchWorker($workerFactory->createWorker(new SumTask()));
$workerManager->launchWorker($workerFactory->createWorker(new SumTask()));

$data = array(
    array(1,2),
    array(4, 5),
    array(9, 5),
);

while (true) {
    $msg = array_pop($data);
    if ($msg !== null) {
        $workerManager->onDataReceived($msg);
    }
    
    $workerManager->dispatchDataToWorkers();
    $workerManager->checkWorkersStatus();
}
