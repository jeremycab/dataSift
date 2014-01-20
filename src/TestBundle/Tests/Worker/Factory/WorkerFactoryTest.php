<?php

namespace DataSift\TestBundle\Tests\Worker\Factory;

use \DataSift\TestBundle\Worker\Factory\WorkerFactory;

/**
 * Description of WorkerFactoryTest
 *
 * @author jcabantous
 */
class WorkerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWorker()
    {
        $timeout = 30;
        $logger = $this->getMockBuilder('\DataSift\TestBundle\Log\Logger\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock();
        
        $factory = new WorkerFactory();
        $worker = $factory->createWorker($logger, $timeout);
        
        $this->assertInstanceOf('\DataSift\TestBundle\Worker\Worker', $worker);
        $this->assertSame($logger, $worker->getLogger());
        $this->assertEquals($timeout, $worker->getTimeout());
    }
    
    /**
     * data provider
     * @return array
     */
    public function providerTestCopyWorker()
    {
        $data = array();
        
        for ($i = 0; $i < 3; $i++) {
            $tasks = array();

            for ($n = 0; $n < $i; $n++) {
                $tasks[] = $this->getMockBuilder('\DataSift\TestBundle\Task\TaskInterface')
                        ->disableOriginalConstructor()
                        ->getMock();
            }
            
            $data[] = array($tasks);
        }

        return $data;
    }
    
    /**
     * @dataProvider providerTestCopyWorker
     * @param array $tasks
     */
    public function testCopyWorker(array $tasks)
    {
        $timeout = 30;
        $worker = $this->getMockBuilder('\DataSift\TestBundle\Worker\Worker')
                ->disableOriginalConstructor()
                ->getMock();
        $logger = $this->getMockBuilder('\DataSift\TestBundle\Log\Logger\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock();
        
        $worker->expects($this->any())
                ->method('getLogger')
                ->will($this->returnValue($logger));
        $worker->expects($this->any())
                ->method('getTimeout')
                ->will($this->returnValue($timeout));
        $worker->expects($this->any())
                ->method('getTasks')
                ->will($this->returnValue($tasks));
        
        $factory = new WorkerFactory();
        $newWorker = $factory->copyWorker($worker);
        
        $this->assertInstanceOf('\DataSift\TestBundle\Worker\Worker', $newWorker);
        $this->assertSame($logger, $newWorker->getLogger());
        $this->assertEquals($timeout, $newWorker->getTimeout());
        $this->assertEquals($tasks, $newWorker->getTasks());
    }
}
