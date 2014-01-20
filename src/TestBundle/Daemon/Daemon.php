<?php

namespace DataSift\TestBundle\Daemon;

use \DataSift\TestBundle\Log\Logger\LoggerInterface;
use \DataSift\TestBundle\Worker\Manager\WorkerManager;
use \DataSift\TestBundle\Worker\Factory\WorkerFactory;
use \DataSift\TestBundle\Task\TaskInterface;
use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Thread\Event\ThreadEventManager;
use DataSift\TestBundle\Socket\Server\SocketServer;
use \DataSift\TestBundle\Worker\Collection\WorkerCollection;

/**
 * manage the pro
 * @author jcabantous
 */
class Daemon
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * collection of tasks to process in each worker
     * @var array
     */
    private $tasks;
    
    /**
     * @var array
     */
    private $config;

    /**
     * initialize the daemon with a logger and configuation
     * @param \DataSift\TestBundle\Log\Logger\LoggerInterface $logger
     * @param array $config
     */
    public function __construct(LoggerInterface $logger, array $config)
    {
        $this->logger = $logger;
        $this->tasks = array();
        $this->config = $config;
    }

    /**
     * add a task to process in each worker
     * @param \DataSift\TestBundle\Task\TaskInterface $task
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * launch the daemon process : create the workers and the socket server
     */
    public function run()
    {
        $server = new SocketServer(
                $this->config['server_adress'], 
                $this->config['server_port'], 
                $this->config['nb_clients_max'], 
                $this->logger);

        $workerFactory = new WorkerFactory();
        $workerManager = new WorkerManager(new ThreadManager(), $workerFactory, $this->logger, new WorkerCollection());
        $threadEventManager = new ThreadEventManager(new ThreadManager());
        //put the thread manager in globals to use it in the pcntl signal header
        $GLOBALS['threadEventManager'] = $threadEventManager;

        //warn the worker manager when a child process down exit
        $threadEventManager->addEventObserver($workerManager);
        //the worker manager is inform when a new task has to be processed
        $server->addListener($workerManager);

        //create the workers
        for ($i = 0; $i < $this->config['nb_workers']; $i++) {
            $worker = $workerFactory->createWorker($this->logger, $this->config['timeout']);
            //add the tasks to perform
            foreach ($this->tasks as $task) {
                $worker->addTask($task);
            }
            //launch the worker
            $workerManager->launchWorker($worker);
        }
        
        //wait to be sure that all workers are correctly set up
        sleep(2);
        
        //when all the workers are lauched, we can start the server
        $server->start();
        while (true) {
            //listen new client connexion
            $server->listen();
            //dispatch the unprocessed tasks to the workers
            $workerManager->dispatchDataToWorkers();
            //check if all the workers are running correctly
            $workerManager->checkWorkersStatus();
        }
    }
}
