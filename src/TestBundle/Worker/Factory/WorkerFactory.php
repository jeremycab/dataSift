<?php

namespace DataSift\TestBundle\Worker\Factory;

use \DataSift\TestBundle\Worker\Worker;
use \DataSift\TestBundle\Queue\QueueManager;
use \DataSift\TestBundle\Queue\Queue;
use \DataSift\TestBundle\Thread\Thread;
use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Worker\Type\WorkerFactoryType;

/**
 * Description of WorkerFactory
 *
 * @author jcabantous
 */
class WorkerFactory
{
    /**
     * 
     * @return Worker
     */
    public function createWorker(\DataSift\TestBundle\Log\Logger\LoggerInterface $logger, $timeout)
    {
        return new Worker(
                new Thread(new ThreadManager()), 
                new QueueManager(new Queue()), 
                new QueueManager(new Queue()), 
                new WorkerFactoryType(),
                $logger,
                $timeout);
    }
}
