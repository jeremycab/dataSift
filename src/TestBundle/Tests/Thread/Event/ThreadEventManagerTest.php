<?php

namespace DataSift\TestBundle\Tests\Thread\Event;

use \DataSift\TestBundle\Thread\Event\ThreadEventManager;

/**
 * Description of ThreadEventManagerTest
 *
 * @author jcabantous
 */
class ThreadEventManagerTest extends \PHPUnit_Framework_TestCase
{
    private $threadManager;

    public function setUp()
    {
        $this->threadManager = $this->getMockBuilder('\DataSift\TestBundle\Thread\Manager\ThreadManager')
                ->disableOriginalConstructor()
                ->getMock();
    }

    public function providerTestOnChildExit()
    {
        $data = array();
        
        for ($i = 0; $i < 3; $i++) {
            $listeners = array();
            $pid = rand(0, 500);
            
            for ($n = 0; $n < $i; $n++) {
                $listener = $this->getMockBuilder('DataSift\TestBundle\Thread\Event\Observer\ThreadEventObserverInterface')
                        ->disableOriginalConstructor()
                        ->getMock();
                
                $listener->expects($this->once())
                        ->method('onChildExit')
                        ->with($pid);
                
                $listeners[] = $listener;
            }
            
            $data[] = array($listeners, $pid);
        }

        return $data;
    }

    /**
     * @dataProvider providerTestOnChildExit
     * @param array $listeners
     * @param type $pid
     */
    public function testOnChildExit(array $listeners, $pid)
    {
        $threadEventManager = new ThreadEventManager($this->threadManager);

        foreach ($listeners as $listener) {
            $threadEventManager->addEventObserver($listener);
        }

        $threadEventManager->onChildExit($pid);
    }
}
