<?php

namespace DataSift\TestBundle\Bootstrap;

use \DataSift\TestBundle\Log\Logger\LoggerInterface;
use \DataSift\TestBundle\Worker\Manager\WorkerManager;
use \DataSift\TestBundle\Worker\Factory\WorkerFactory;
use \DataSift\TestBundle\Task\TaskInterface;
use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Thread\Event\ThreadEventManager;
use DataSift\TestBundle\Server\SocketServer;

/**
 * Description of Bootstrap
 *
 * @author jcabantous
 */
class Bootstrap
{
    private $logger;
    private $tasks;
    private $config;

    public function __construct(LoggerInterface $logger, array $config)
    {
        $this->logger = $logger;
        $this->tasks = array();
        $this->config = $config;
    }

    public function addTask(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }

    public function run()
    {
        $server = new SocketServer('localhost', null, 10, $this->logger);

        $workerManager = new WorkerManager(new ThreadManager(), $this->logger);
        $threadEventManager = new ThreadEventManager(new ThreadManager());
        $workerFactory = new WorkerFactory();

        $threadEventManager->addEventObserver($workerManager);
        $server->addListener($workerManager);

        for ($i = 0; $i < $this->config['nb_workers']; $i++) {
            $worker = $workerFactory->createWorker($this->logger, $this->config['timeout']);
            foreach ($this->tasks as $task) {
                $worker->addTask($task);
            }
            $workerManager->launchWorker($worker);
        }
        
        sleep(3);
        
        $server->start();
        while (true) {
            $server->listen();
            $workerManager->dispatchDataToWorkers();
            $workerManager->checkWorkersStatus();
        }
    }
}
