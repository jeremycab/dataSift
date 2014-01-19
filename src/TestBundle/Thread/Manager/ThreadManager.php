<?php

namespace DataSift\TestBundle\Thread\Manager;

/**
 * class managing the pcntl functions
 * @author jcabantous
 */
class ThreadManager
{
    /**
     * Forks the currently running process
     * @return int : the PID created in the parent thread, 0 in the child
     */
    public function fork()
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \RuntimeException('Unable to fork');
        }
        return $pid;
    }
    
    /**
     * set a callback when a child is dead
     * @param mixed $object : the object to call
     * @param string $method : the method to call
     */
    public function onChildExit($object, $method)
    {
        declare(ticks = 1);
        pcntl_signal(SIGCHLD, array(get_class($object),$method)); 
    }
    
    /**
     * get the Process id of the current thread
     * @return type
     */
    public function getPid()
    {
        return getmypid();
    }
}
