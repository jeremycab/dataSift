<?php

namespace DataSift\TestBundle\Tests\Server\Redis;

use  \DataSift\TestBundle\Server\Redis\RedisServer;

/**
 *
 * @author jeremy
 */
class RedisServerTest extends \PHPUnit_Framework_TestCase
{
    private $redis;
    private $storage; 
    private $listener;
    
    public function setUp()
    {
        $this->redis = $this->getMockBuilder('\Redis')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->storage = $this->getMockBuilder('\SplObjectStorage')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->listener = $this->getMockBuilder('\DataSift\TestBundle\Server\Listener\ServerListenerInterface')
                ->disableOriginalConstructor()
                ->getMock();
    }
    
    /**
     * test method "addListener"
     */
    public function testAddListener()
    {
        $this->storage->expects($this->once())
                ->method('attach')
                ->with($this->listener);
        
        $server = new RedisServer($this->redis, $this->storage);
        $server->addListener($this->listener);
    }
    
    /*public function testListen()
    {
        $this->redis->expects($this->at(0))
                ->method('lPop')
                ->with('bitly')
                ->will($this->returnValue('test'));
        
        $this->redis->expects($this->at(1))
                ->method('lPop')
                ->with('bitly')
                ->will($this->returnValue(null));
        
        $server = $this->getMock('\DataSift\TestBundle\Server\Redis\RedisServer', 
                array('onDataReceived'), 
                array($this->redis, $this->storage));
        
        $server->expects($this->once())
                ->method('onDataReceived')
                ->with('test');
        
        $server->listen();
    }*/
    
    public function testOnDataReceived()
    {
        $this->listener->expects($this->once())
                ->method('onDataReceived')
                ->with('dummy');
        
        $server = new RedisServer($this->redis, new \SplObjectStorage());
        $server->addListener($this->listener);
        $server->onDataReceived('dummy');
    }
}
