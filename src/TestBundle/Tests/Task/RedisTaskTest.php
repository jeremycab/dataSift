<?php

namespace DataSift\TestBundle\Tests\Task;

use \DataSift\TestBundle\Task\RedisTask;

/**
 *
 * @author jeremy
 */
class RedisTaskTest extends \PHPUnit_Framework_TestCase
{
    private $redis;
    
    public function setUp()
    {
        $this->redis = $this->getMockBuilder('\Redis')
                ->disableOriginalConstructor()
                ->getMock();
    }
    
    public function testWork()
    {
        $this->redis->expects($this->once())
                ->method('lPush')
                ->with('dataSiftList', 'data');
        
        $task = new RedisTask($this->redis);
        $return = $task->work('data');
        
        $this->assertEquals('done', $return);
    }
}

