<?php

namespace DataSift\TestBundle\Task;

/**
 * Description of SumTask
 *
 * @author jcabantous
 */
class SumTask implements TaskInterface
{
    public function work(array $params)
    {
       sleep(20);
        if (!is_array($params[0])) {
            return $params[0];
        }
        
        return array_sum($params[0]);
    }    
}
