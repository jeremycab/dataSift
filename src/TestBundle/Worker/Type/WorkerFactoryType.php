<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

/**
 * manage the creation of worker type
 * @author jcabantous
 */
class WorkerFactoryType
{
    /**
     * get an instance of the parent type
     * @param \DataSift\TestBundle\Worker\Worker $worker
     * @return \DataSift\TestBundle\Worker\Type\WorkerParentType
     */
    public function getTypeParent(Worker $worker)
    {
        return new WorkerParentType($worker);
    }
    
    /**
     * get an instance of the child type
     * @param \DataSift\TestBundle\Worker\Worker $worker
     * @return \DataSift\TestBundle\Worker\Type\WorkerChildType
     */
    public function getTypeChild(Worker $worker)
    {
        return new WorkerChildType($worker);
    }
}
