<?php

namespace DataSift\TestBundle\Task;

/**
 * Description of SumTask
 *
 * @author jcabantous
 */
class DummyTask implements TaskInterface
{
    public function work($data)
    {
        return md5($data);
    }    
}
