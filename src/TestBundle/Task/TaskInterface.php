<?php

namespace DataSift\TestBundle\Task;

/**
 * interface to implement by each task processed in the workers
 * @author jcabantous
 */
interface TaskInterface
{
    /**
     * process a set of data passed in arguments
     * @param array $params : array with all the parameters sent by the client
     */
    public function work(array $params);
}
