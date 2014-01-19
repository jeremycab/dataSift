<?php

namespace DataSift\TestBundle\Worker\Type;

use DataSift\TestBundle\Worker\Worker;

/**
 * strategy pattern use for worker behavior depending on the worker is in child or parent process
 * @author jeremy
 */
abstract class WorkerAbstractType 
{
    /**
     * @var Worker
     */
    protected $worker;
    
    /**
     *
     * @var \DataSift\TestBundle\Log\Logger\LoggerInterface
     */
    protected $logger;
    
    /**
     * init the worker to manage
     * @param \DataSift\TestBundle\Worker\Worker $worker
     */
    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
        $this->logger = $worker->getLogger();
    }
    
    /**
     * process the queue messages waiting to be processed
     */
    abstract public function processQueueMessages();
    
    /**
     * say if the worker is in timeout
     * @return : boolean
     */
    abstract public function isInTimeOut();
    
    /**
     * launch the worker process
     */
    abstract public function run();
}
