<?php

namespace DataSift\TestBundle\Worker\Factory;

use \DataSift\TestBundle\Worker\Worker;
use \DataSift\TestBundle\Queue\QueueManager;
use \DataSift\TestBundle\Queue\Queue;
use \DataSift\TestBundle\Thread\Thread;
use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Worker\Type\WorkerFactoryType;
use \DataSift\TestBundle\Log\Logger\LoggerInterface;

/**
 * manage the instanciation of the workers
 * @author jcabantous
 */
class WorkerFactory
{
    /**
     * return a new instance of a worker
     * @param \DataSift\TestBundle\Log\Logger\LoggerInterface $logger : the logger to use
     * @param type $timeout : the timeout delay to send a keep-alive message
     * @return \DataSift\TestBundle\Worker\Worker
     */
    public function createWorker(LoggerInterface $logger, $timeout)
    {
        return new Worker(
                new Thread(new ThreadManager()), 
                new QueueManager(new Queue()), 
                new QueueManager(new Queue()), 
                new WorkerFactoryType(),
                $logger,
                $timeout);
    }
    
    /**
     * create a copy of an existing worker 
     * @param \DataSift\TestBundle\Worker\Worker $worker
     * @return Worker
     */
    public function copyWorker(Worker $worker)
    {
        $newWorker = $this->createWorker($worker->getLogger(), $worker->getTimeout());
        foreach ($worker->getTasks() as $task) {
            $newWorker->addTask($task);
        }
        
        return $newWorker;
    }
}
