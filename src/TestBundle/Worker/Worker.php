<?php

namespace DataSift\TestBundle\Worker;

use DataSift\TestBundle\Queue\QueueManager;
use \DataSift\TestBundle\Task\TaskInterface;
use \DataSift\TestBundle\Thread\Thread;
use DataSift\TestBundle\Worker\Type\WorkerFactoryType;
use \DataSift\TestBundle\Log\Logger\LoggerInterface;

/**
 * Description of Worker
 *
 * @author jcabantous
 */
class Worker
{
    const MSG_QUIT = 'youhavetoquitnow!!!!!';
    /**
     * @var QueueManager 
     */
    private $queueIn;
    
    /**
     * @var QueueManager 
     */
    private $queueOut;
    
    /**
     * @var int
     */
    private $timeout;
    
    /**
     * @var Thread
     */
    private $thread;
    
    /**
     * @var array
     */
    private $tasks;
    
    /**
     * @var WorkerFactoryType 
     */
    private $typeFactory;
    
    /**
     * @var Type\WorkerAbstractType 
     */
    private $type;
    
    /**
     * @var boolean
     */
    private $isActive;
    
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
            Thread $thread, 
            QueueManager $queueIn, 
            QueueManager $queueOut, 
            WorkerFactoryType $typeFactory, 
            LoggerInterface $logger,
            $timeout = 5)
    {
        $this->queueIn = $queueIn;
        $this->queueOut = $queueOut;
        $this->timeout = $timeout;
        $this->thread = $thread;
        $this->tasks = array();
        $this->typeFactory = $typeFactory;
        $this->logger = $logger;
        $this->isActive = true;
        $this->type = $this->typeFactory->getTypeParent($this);
    }
    
    public function getQueueIn()
    {
        return $this->queueIn;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getTasks()
    {
        return $this->tasks;
    }
    
    public function getLogger()
    {
        return $this->logger;
    }

    public function sendMsg($msg)
    {
        $this->queueIn->sendMsg($msg);
        $this->dateLastMsgSent = time();
    }
    
    public function addTask(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }
    
    /**
     * @return Thread
     */
    public function getThread()
    {
        return $this->thread;
    }
    
    /**
     * 
     * @return QueueManager
     */
    public function getQueueOut()
    {
        return $this->queueOut;
    }

    public function processQueue()
    {
        $this->type->processQueueMessages();
    }
    
    public function isAvailable()
    {
        return ($this->queueIn->countNbMessagesInQueue() == 0
                && !$this->isInTimeOut()
                && $this->isActive);
    }
    
    public function sendMsgStillAlive()
    {
        $this->queueOut->sendMsg('Worker on ' . $this->thread . " is still alive");
    }
    
    public function isInTimeOut()
    {
        return $this->type->isInTimeOut();
    }
    
    public function __toString() {
        return 'Worker on ' . $this->thread;
    }
    
    public function run()
    {
        $this->type->run();
    }
    
    public function setIsInChildProcess()
    {
        $this->type = $this->typeFactory->getTypeChild($this);
    }
    
    public function setIsInactive()
    {
        $this->isActive = false;
    }
    
    public function isActive()
    {
        return $this->isActive;
    }
    
    public function stop()
    {
        exit(0);
    }
    
    public function reloadQueues()
    {
        $this->queueIn->loadNewQueueId();
        $this->queueOut->loadNewQueueId();
    }
}
