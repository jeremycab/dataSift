<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

/**
 * Description of WorkerTypeFactory
 *
 * @author jcabantous
 */
class WorkerFactoryType
{
    public function getTypeParent(Worker $worker)
    {
        return new WorkerParentType($worker);
    }
    
    public function getTypeChild(Worker $worker)
    {
        return new WorkerChildType($worker);
    }
}
