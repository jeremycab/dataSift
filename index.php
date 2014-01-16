<?php

use \DataSift\TestBundle\Worker\Manager\WorkerManager;
use DataSift\TestBundle\Log\Logger\EchoLogger;
use \DataSift\TestBundle\Worker\Factory\WorkerFactory;
use \DataSift\TestBundle\Task\SumTask;
use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Thread\Event\ThreadEventManager;

require 'autoloader.php';

$nbWorker = 2;

$workerManager = new WorkerManager(new ThreadManager(), new EchoLogger());
$threadEventManager = new ThreadEventManager(new ThreadManager());
$workerFactory = new WorkerFactory();

$threadEventManager->addEventObserver($workerManager);

for ($i=0; $i < $nbWorker; $i++) {
    $worker = $workerFactory->createWorker();
    $worker->addTask(new SumTask());
    $workerManager->launchWorker($worker);
}

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
