<?php

namespace DataSift\TestBundle\Worker\Manager;

use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use \DataSift\TestBundle\Log\Logger\LoggerInterface;
use \DataSift\TestBundle\Worker\Worker;
use \DataSift\TestBundle\Thread\Event\Observer\ThreadEventObserverInterface;
use \DataSift\TestBundle\Socket\Server\Listener\ServerListenerInterface;
use \DataSift\TestBundle\Worker\Factory\WorkerFactory;

/**
 * Description of WorkerManager
 *
 * @author jcabantous
 */
class WorkerManager implements ThreadEventObserverInterface, ServerListenerInterface
{

    /**
     * @var \DataSift\TestBundle\Thread\Manager\ThreadManager
     */
    private $threadManager;

    /**
     * @var array
     */
    private $workers;

    /**
     * @var array
     */
    private $data;

    /**
     * \DataSift\TestBundle\Log\Logger\LoggerInterface
     */
    private $logger;
    private $workerFactory;

    public function __construct(ThreadManager $threadManager, WorkerFactory $workerFactory, LoggerInterface $logger)
    {
        $this->threadManager = $threadManager;
        $this->workers = array();
        $this->data = array();
        $this->logger = $logger;
        $this->workerFactory = $workerFactory;
    }

    /**
     * add a worker 
     * @param \DataSift\TestBundle\Worker\Worker $worker
     */
    public function launchWorker(Worker $worker)
    {
        //duplicate process
        $pid = $this->threadManager->fork();

        if ($pid != 0) {
            $worker->getThread()->setPid($pid);
            $worker->setIsActive();
            $this->workers[$pid] = $worker;
        } else {
            $worker->setIsInChildProcess();
            $worker->getThread()->loadCurrentPid();
            $worker->run();
            $worker->stop();
        }
    }

    public function checkWorkersStatus()
    {
        /* @var $worker Worker */
        foreach ($this->workers as $worker) {
            $worker->processQueue();
            if ($worker->isActive() && $worker->isInTimeOut()) {
                $this->onChildExit($worker->getThread()->getPid());
            }
        }
    }

    public function onDataReceived($data)
    {
        $this->data[] = $data;
    }

    public function dispatchDataToWorkers()
    {
        foreach ($this->data as $key => $data) {
            /* @var $worker Worker */
            foreach ($this->workers as $worker) {
                if ($worker->isAvailable()) {
                    $this->logger->log("send task to " . $worker);
                    $worker->sendMsg($data);
                    unset($this->data[$key]);
                    break;
                }
            }
        }
    }

    public function onChildExit($pid)
    {
        if (isset($this->workers[$pid])) {
            /* @var $worker Worker */
            $worker = $this->workers[$pid];

            if ($worker->isActive()) {
                $worker->setIsInactive();
                $this->logger->log($worker . ' is dead or not responding');

                $newWorker = $this->workerFactory->createWorker($worker->getLogger(), $worker->getTimeout());
                $newWorker->setTask($worker->getTasks());
                $this->launchWorker($newWorker);
                sleep(1);
            }
        }
    }
}
