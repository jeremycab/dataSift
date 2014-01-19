<?php

namespace DataSift\TestBundle\Thread;

use \DataSift\TestBundle\Thread\Manager\ThreadManager;

/**
 * represent a linux thread
 * @author jcabantous
 */
class Thread
{
    /**
     * @var int
     */
    private $pid;
    
    /**
     * @var \DataSift\TestBundle\Thread\Manager\ThreadManager
     */
    private $threadManager;
    
    /**
     * init the thread with a manager
     * @param \DataSift\TestBundle\Thread\Manager\ThreadManager $threadManager
     */
    public function __construct(ThreadManager $threadManager)
    {
        $this->threadManager = $threadManager;
        //load the current PID
        $this->pid = $this->threadManager->getPid();
    }

    /**
     * get the process id of the thread
     * @return type
     */
    public function getPid()
    {
        return $this->pid;
    }
    
    /**
     * set an id to the thread
     * @param type $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }
    
    /**
     * magic method 
     * @return string
     */
    public function __toString()
    {
        return 'Thread (PID=' . $this->pid . ')';
    }

    /**
     * assign to the thread the current PID
     */
    public function loadCurrentPid()
    {
        $this->pid = $this->threadManager->getPid();
    }
}
