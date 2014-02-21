<?php

namespace DataSift\TestBundle\Server\Redis;

use \DataSift\TestBundle\Server\ServerInterface;
use \DataSift\TestBundle\Server\Listener\ServerListenerInterface;

/**
 *
 * @author jeremy
 */
class RedisServer implements ServerInterface
{

    private $redis;
    private $listeners;

    public function __construct(\Redis $redis, \SplObjectStorage $storage)
    {
        $this->redis = $redis;
        $this->listeners = $storage;
    }

    public function listen()
    {
        while ($this->redis->lLen('bitly') > 0) {
            $data = $this->redis->lPop('bitly');
            $this->onDataReceived($data);
        }
        var_dump('done');
    }

    public function onDataReceived($data)
    {
        /* @var $listener \DataSift\TestBundle\Server\Listener\ServerListenerInterface */
        foreach ($this->listeners as $listener) {
            $listener->onDataReceived($data);
        }
    }

    public function start()
    {
        
    }

    public function addListener(ServerListenerInterface $listener)
    {
        $this->listeners->attach($listener);
    }

}

