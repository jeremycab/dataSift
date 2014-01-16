<?php

namespace DataSift\TestBundle\Worker;

use DataSift\TestBundle\Queue\QueueManager;
use \DataSift\TestBundle\Task\TaskInterface;
use \DataSift\TestBundle\Thread\Thread;

/**
 * Description of Worker
 *
 * @author jcabantous
 */
class Worker
{
    private $queueIn;
    private $queueOut;
    private $dateLastMsgSent;
    private $timeout;
    private $thread;
    private $tasks;

    public function __construct(Thread $thread, QueueManager $queueIn, QueueManager $queueOut, $timeout = 5)
    {
        $this->queueIn = $queueIn;
        $this->queueOut = $queueOut;
        $this->dateLastMsgSent = time();
        $this->timeout = $timeout;
        $this->thread = $thread;
        $this->tasks = array();
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
     * @return \SplQueue
     */
    public function getMessagesFrom()
    {
        return $this->queueOut->getCurrentMsg();
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
        $queue = $this->queueIn->getCurrentMsg();
        foreach ($queue as $msg) {
            foreach ($this->tasks as $task) {
                $result = $task->work(array($msg));
                $this->queueOut->sendMsg('Message traité par ' . $this->thread . '. Result : ' . $result);
            }
        }
    }
    
    public function isAvailable()
    {
        return ($this->queueIn->countNbMessagesInQueue() == 0
                && !$this->isInTimeOut());
    }
    
    public function sendMsgStillAlive()
    {
        $this->queueOut->sendMsg('Worker on ' . $this->thread . " is still alive");
        $this->dateLastMsgSent = time();
    }
    
    public function isInTimeOut()
    {
        $time = time();
        return (($time - $this->dateLastMsgSent) > $this->timeout);
    }
    
    public function __toString() {
        return 'Worker on ' . $this->thread;
    }
    
    public function isRunning()
    {
        return true;
    }
    
    public function setIsInChildProcess()
    {
        
    }
}
