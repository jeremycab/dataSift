<?php

namespace DataSift\TestBundle\Worker\Type;

use DataSift\TestBundle\Worker\Worker;

/**
 * Description of Abstract
 *
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
    
    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
        $this->logger = $worker->getLogger();
    }
    
    abstract public function processQueueMessages();
    
    abstract public function isInTimeOut();
    
    abstract public function run();
}
