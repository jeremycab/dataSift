<?php

namespace DataSift\TestBundle\Worker\Collection;

use \DataSift\TestBundle\Worker\Worker;

/**
 * Description of Collection
 *
 * @author jcabantous
 */
class WorkerCollection
{
    /**
     * @var array
     */
    private $workers; 
    
    public function __construct()
    {
        $this->workers = array();
    }

    /**
     * add a worker to the collection
     * @param \DataSift\TestBundle\Worker\Worker $worker
     */
    public function addWorker(Worker $worker)
    {
        $this->workers[$worker->getThread()->getPid()] = $worker;
    }
    
    /**
     * get a worker from its process id
     * @param type $pid
     * @return Worker
     */
    public function getWorkerFromPid($pid)
    {
        if (isset($this->workers[$pid])) {
            return $this->workers[$pid];
        }
    }
    
    public function getAll()
    {
        return $this->workers;
    }
}
