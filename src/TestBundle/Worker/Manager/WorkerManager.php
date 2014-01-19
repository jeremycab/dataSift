<?php

namespace DataSift\TestBundle\Worker\Manager;

use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Log\Logger\LoggerInterface;
use \DataSift\TestBundle\Worker\Worker;
use \DataSift\TestBundle\Thread\Event\Observer\ThreadEventObserverInterface;
use \DataSift\TestBundle\Socket\Server\Listener\ServerListenerInterface;
use \DataSift\TestBundle\Worker\Factory\WorkerFactory;

/**
 * manage a pool of workers
 * @author jcabantous
 */
class WorkerManager implements ThreadEventObserverInterface, ServerListenerInterface
{

    /**
     * @var \DataSift\TestBundle\Thread\Manager\ThreadManager
     */
    private $threadManager;

    /**
     * the list of workers to manage
     * @var array
     */
    private $workers;

    /**
     * list of data to process
     * @var array
     */
    private $data;

    /**
     * \DataSift\TestBundle\Log\Logger\LoggerInterface
     */
    private $logger;
    
    /**
     * @var WorkerFactory
     */
    private $workerFactory;

    /**
     * init the worker manager 
     * @param \DataSift\TestBundle\Thread\Manager\ThreadManager $threadManager
     * @param \DataSift\TestBundle\Worker\Factory\WorkerFactory $workerFactory
     * @param \DataSift\TestBundle\Log\Logger\LoggerInterface $logger
     */
    public function __construct(ThreadManager $threadManager, WorkerFactory $workerFactory, LoggerInterface $logger)
    {
        $this->threadManager = $threadManager;
        $this->workers = array();
        $this->data = array();
        $this->logger = $logger;
        $this->workerFactory = $workerFactory;
    }

    /**
     * launch a worker in a new process
     * @param \DataSift\TestBundle\Worker\Worker $worker
     */
    public function launchWorker(Worker $worker)
    {
        //duplicate process
        $pid = $this->threadManager->fork();

        //check if we are in the parent process
        if ($pid != 0) {
            //assign to the thread its PID
            $worker->getThread()->setPid($pid);
            //put the worker in active status
            $worker->setIsActive();
            //add it to the collections
            $this->workers[$pid] = $worker;
        } else { // if we are in the child process
            //set the child strategy to the process
            $worker->setIsInChildProcess();
            //load the new PID
            $worker->getThread()->loadCurrentPid();
            //run the worker
            $worker->run();
            //when the worker has finished its duty => stop it !
            $worker->stop();
        }
    }

    /**
     * check that all workers are responding correctly
     */
    public function checkWorkersStatus()
    {
        /* @var $worker Worker */
        foreach ($this->workers as $worker) {
            //process the messages of the workers
            $worker->processQueue();
            //if the worker is set to active but is in timeout
            if ($worker->isActive() && $worker->isInTimeOut()) {
                //call the function in charge of handling children's death
                $this->onChildExit($worker->getThread()->getPid());
            }
        }
    }

    /**
     * implements ServerListenerInterface
     * @param type $data
     */
    public function onDataReceived($data)
    {
        $this->data[] = $data;
    }

    /**
     * disptach the data waiting to be processed to the workers
     */
    public function dispatchDataToWorkers()
    {
        foreach ($this->data as $key => $data) {
            /* @var $worker Worker */
            foreach ($this->workers as $worker) {
                //check if the worker is available to process data
                if ($worker->isAvailable()) {
                    $this->logger->log("send task to " . $worker);
                    //send the message
                    $worker->sendMsg($data);
                    unset($this->data[$key]);
                    break;
                }
            }
        }
    }

    /**
     * implements ThreadEventObserverInterface
     * handle process when a child process dies
     * @param int $pid
     */
    public function onChildExit($pid)
    {
        //chekc if the process is in the pool of data
        if (isset($this->workers[$pid])) {
            /* @var $worker Worker */
            $worker = $this->workers[$pid];

            //if the worker is set as active
            if ($worker->isActive()) {
                //put it as inactive
                $worker->setIsInactive();
                $this->logger->log($worker . ' is dead or not responding');
                //pop the messages of the inactive worker and set them into the collections of data to process
                foreach ($worker->getQueueIn()->getCurrentMsg() as $message) {
                    $this->data[] = $message;
                }
                
                //create and launch a new one
                $newWorker = $this->workerFactory->copyWorker($worker);
                $this->launchWorker($newWorker);
                //wait to be sure the worker is correctly set up
                sleep(1);
            }
        }
    }
}
