<?php

namespace DataSift\TestBundle\Task;

/**
 * push data into a redis Queue
 * @author jeremy
 */
class RedisTask implements TaskInterface
{
    private $redis;
    
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function work($data)
    {
        $object = json_decode($data, true);
        //do stuff
        
        $this->redis->lPush('dataSiftList', $data);
        return 'done';
    }

    public function getName()
    {
        return 'redis';
    }    
}
