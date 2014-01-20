<?php

namespace DataSift\TestBundle\Worker;

use DataSift\TestBundle\Queue\QueueManager;
use \DataSift\TestBundle\Task\TaskInterface;
use \DataSift\TestBundle\Thread\Thread;
use DataSift\TestBundle\Worker\Type\WorkerFactoryType;
use \DataSift\TestBundle\Log\Logger\LoggerInterface;

/**
 * represent a worker run either in the parent or in the child thread 
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

    /**
     * init the worker
     * @param \DataSift\TestBundle\Thread\Thread $thread : the thread where the worker is running
     * @param \DataSift\TestBundle\Queue\QueueManager $queueIn : the queue storing the messages received by the worker
     * @param \DataSift\TestBundle\Queue\QueueManager $queueOut : the queue storing the messages sent by the worker
     * @param \DataSift\TestBundle\Worker\Type\WorkerFactoryType $typeFactory
     * @param \DataSift\TestBundle\Log\Logger\LoggerInterface $logger
     * @param int $timeout : delay after what the worker is tagged as inactive
     */
    public function __construct(
            Thread $thread, 
            QueueManager $queueIn, 
            QueueManager $queueOut, 
            WorkerFactoryType $typeFactory, 
            LoggerInterface $logger,
            $timeout)
    {
        if (!is_numeric($timeout)) {
            throw new \InvalidArgumentException('the timeout has to be a numeric value');
        }
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
    
    /**
     * get the queue of the messages received
     * @return QueueManager
     */
    public function getQueueIn()
    {
        return $this->queueIn;
    }

    /**
     * get the delay of timeout
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * get the task to perform when a message is received
     * @return array
     */
    public function getTasks()
    {
        return $this->tasks;
    }
    
    /**
     * get the logger used by the worker
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * send a message to the worker
     * @param type $msg
     */
    public function sendMsgTo($msg)
    {
        $this->queueIn->sendMsg($msg);
    }

    /**
     * send message from the worker
     * @param type $msg
     */
    public function sendMsgFrom($msg)
    {
        $this->queueOut->sendMsg($msg);
    }
    
    /**
     * add a task to perform
     * @param \DataSift\TestBundle\Task\TaskInterface $task
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }
    
    /**
     * get the thread of the worker
     * @return Thread
     */
    public function getThread()
    {
        return $this->thread;
    }
    
    /**
     * get the queue of the messages sent by the worker
     * @return QueueManager
     */
    public function getQueueOut()
    {
        return $this->queueOut;
    }

    /**
     * process the messages in queue
     */
    public function processQueue()
    {
        $this->type->processQueueMessages();
    }
    
    /**
     * say if the worker is available to process message
     * @return type
     */
    public function isAvailable()
    {
        return ($this->queueIn->countNbMessagesInQueue() == 0
                && !$this->isInTimeOut()
                && $this->isActive);
    }
    
    /**
     * inform the manager that the worker is still alive
     */
    public function sendMsgStillAlive()
    {
        $this->queueOut->sendMsg('Worker on ' . $this->thread . " is still alive");
    }
    
    /**
     * say if the worker is in timeout
     * @return boolean
     */
    public function isInTimeOut()
    {
        return $this->type->isInTimeOut();
    }
    
    /**
     * magic method
     * @return string
     */
    public function __toString() {
        return 'Worker on ' . $this->thread;
    }
    
    /**
     * do the worker process
     */
    public function run()
    {
        $this->type->run();
    }
    
    /**
     * inform the worker that the worker is running into the child process
     */
    public function setIsInChildProcess()
    {
        $this->type = $this->typeFactory->getTypeChild($this);
    }
    
    /**
     * tag the worker as inactive
     */
    public function setIsInactive()
    {
        $this->isActive = false;
    }
    
    /**
     * tag the worker as active
     */
    public function setIsActive()
    {
        $this->isActive = true;
    }
    
    /**
     * say if the worker is active or not
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }
    
    /**
     * stop the worker
     */
    public function stop()
    {
        exit(0);
    }
    
    /**
     * get the timestamp of the last message received by the worker
     * @return int
     */
    public function getTimestampLastMsgReceived()
    {
        return $this->getQueueOut()->getLastMsgReceived();
    }
    
    /**
     * get the timestamp of the last message sent by the worker
     * @return int
     */
    public function getTimestampLastMsgSent()
    {
        return $this->getQueueOut()->getLastMsgSent();
    }
    
    /**
     * get all the  messages sent by the worker and still in the queue out
     * @return \SplQueue
     */
    public function getMsgsSent()
    {
        return $this->getQueueOut()->getCurrentMsg();
    }
    
    /**
     * get all the  messages received by the worker and pending to be processed
     * @return \SplQueue
     */
    public function getMsgsReceived()
    {
        return $this->getQueueIn()->getCurrentMsg();
    }
}
