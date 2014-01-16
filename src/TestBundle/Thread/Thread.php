<?php

namespace DataSift\TestBundle\Thread;

use \DataSift\TestBundle\Thread\Manager\ThreadManager;

/**
 * Description of Thread
 *
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
    
    public function __construct(ThreadManager $threadManager)
    {
        $this->threadManager = $threadManager;
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
    
    public function setPid($pid)
    {
        $this->pid = $pid;
    }
    
    public function __toString()
    {
        return 'Thread (PID=' . $this->pid . ')';
    }
    
    public function isAlive()
    {
        return $this->threadManager->isProcessRunning($this->pid);
    }
    
    public function loadCurrentPid()
    {
        $this->pid = $this->threadManager->getPid();
    }
}
