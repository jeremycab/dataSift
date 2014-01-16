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
    private $task;

    public function __construct(Thread $thread, TaskInterface $task, QueueManager $queueIn, QueueManager $queueOut, $timeout = 5)
    {
        $this->queueIn = $queueIn;
        $this->queueOut = $queueOut;
        $this->dateLastMsgSent = time();
        $this->timeout = $timeout;
        $this->task = $task;
        $this->thread = $thread;
    }
    
    public function sendMsg($msg)
    {
        $this->queueIn->sendMsg($msg);
        $this->dateLastMsgSent = time();
    }
    
    /**
     * 
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
          //  $this->thread->log('Msg received by ' . $this->thread);
            $result = $this->task->work(array($msg));
            $this->queueOut->sendMsg('Message traité par ' . $this->thread . '. Result : ' . $result);
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
}
