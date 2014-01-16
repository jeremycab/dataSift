<?php

namespace DataSift\TestBundle\Thread\Manager;

/**
 * Description of ThreadManager
 *
 * @author jcabantous
 */
class ThreadManager
{
    public function fork()
    {
        $pid = pcntl_fork();
        return $pid;
    }
    
    /**
     * say if a process id is currently running
     * @param int $pid
     * @return boolean
     */
    public function isProcessRunning($pid)
    {
        exec('ps -o pid= -p ' . $pid, $output);
        return (!empty($output));
    }
    
    public function onChildDied($object, $method)
    {
        declare(ticks = 1);
        pcntl_signal(SIGCHLD, array(get_class($object),$method)); 
    }
    
    public function getPid()
    {
        return getmypid();
    }
}
