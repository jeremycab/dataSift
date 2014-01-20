<?php

namespace DataSift\TestBundle\Tests\Thread;

use \DataSift\TestBundle\Thread\Thread;

/**
 * Description of ThreadManagerTest
 *
 * @author jcabantous
 */
class ThreadTest extends \PHPUnit_Framework_TestCase
{
    private $threadManager;

    public function setUp()
    {
        $this->threadManager = $this->getMockBuilder('\DataSift\TestBundle\Thread\Manager\ThreadManager')
                ->disableOriginalConstructor()
                ->getMock();
    }
    
    public function testGetAndSetPid()
    {
        $this->threadManager->expects($this->exactly(2))
                ->method('getPid')
                ->will($this->returnValue(27));
        
        $thread = new Thread($this->threadManager);
        $this->assertEquals(27, $thread->getPid());
        
        $thread->setPid(31);
        $this->assertEquals(31, $thread->getPid());
        
        $thread->loadCurrentPid();
        $this->assertEquals(27, $thread->getPid());
    }
    
    public function testToString()
    {
        $thread = new Thread($this->threadManager);
        $this->assertTrue(is_string('test ' . $thread));
    }
}
