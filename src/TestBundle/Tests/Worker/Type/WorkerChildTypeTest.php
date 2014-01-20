<?php

namespace DataSift\TestBundle\Tests\Worker\Type;

use \DataSift\TestBundle\Worker\Type\WorkerChildType;
use \DataSift\TestBundle\Worker\Worker;

/**
 * Description of WorkerChildTypeTest
 *
 * @author jcabantous
 */
class WorkerChildTypeTest extends \PHPUnit_Framework_TestCase
{
    private $worker;
    private $logger;
    private $queueOut;
    
    public function setUp()
    {
        $this->worker = $this->getMockBuilder('DataSift\TestBundle\Worker\Worker')
                ->disableOriginalConstructor()
                ->getMock();

        $this->logger = $this->getMockBuilder('\DataSift\TestBundle\Log\Logger\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock();

        $this->queueOut = $this->getMockBuilder('\DataSift\TestBundle\Queue\QueueManager')
                ->disableOriginalConstructor()
                ->getMock();

        $this->worker->expects($this->any())
                ->method('getLogger')
                ->will($this->returnValue($this->logger));
    }
    
    public function testIsInTimeOut()
    {
        $this->worker->expects($this->any())
                ->method('getTimeout')
                ->will($this->returnValue(5));

        $this->worker->expects($this->any())
                ->method('getTimestampLastMsgSent')
                ->will($this->returnValue(time() - 6));

        $childType = new WorkerChildType($this->worker);
        $result = $childType->isInTimeOut();

        $this->assertTrue($result);
    }
    
    public function providerTestProcessQueueWorkerRegularMessage()
    {
        $data = array();
        $messages = array();
        
        for ($i = 1; $i < 3; $i++) {
            $tasks = array();

            for ($n = 1; $n < $i; $n++) {
                $tasks[] = $this->getMockBuilder('\DataSift\TestBundle\Task\TaskInterface')
                        ->disableOriginalConstructor()
                        ->getMock();
                $messages[] = 'test ' . $n;
            }
            
            $data[] = array($messages, $tasks);
        }

        return $data;
    }
    
    /**
     * @dataProvider providerTestProcessQueueWorkerRegularMessage
     * @param type $data
     * @param type $tasks
     */
    public function testProcessQueueWorkerRegularMessage($data, $tasks)
    {
        $queue = new \SplQueue();
        foreach ($data as $value) {
            $queue->enqueue($value);
        }
        
        foreach ($tasks as $task) {
            $task->expects($this->exactly(count($data)))
                ->method('work');
        }
        
        $this->worker->expects($this->once())
                ->method('getMsgsReceived')
                ->will($this->returnValue($queue));
        
        $this->worker->expects($this->exactly($queue->count()))
                ->method('getTasks')
                ->will($this->returnValue($tasks));
        
        $this->worker->expects($this->exactly($queue->count()))
                ->method('sendMsgFrom')
                ->will($this->returnValue($tasks));
        
        $childType = new WorkerChildType($this->worker);
        $childType->processQueueMessages();
    }
    
    public function providerTestProcessQueueWorkerInactive()
    {
        $data = array();
        $messages = array(Worker::MSG_QUIT);
        
        for ($i = 1; $i < 3; $i++) {
            $tasks = array();

            for ($n = 1; $n < $i; $n++) {
                $tasks[] = $this->getMockBuilder('\DataSift\TestBundle\Task\TaskInterface')
                        ->disableOriginalConstructor()
                        ->getMock();
                $messages[] = 'test ' . $n;
            }
            
            $data[] = array($messages, $tasks);
        }

        return $data;
    }
    
    /**
     * @dataProvider providerTestProcessQueueWorkerInactive
     * @param type $data
     * @param type $tasks
     */
    public function testProcessQueueWorkerInactive($data, $tasks)
    {
        $queue = new \SplQueue();
        foreach ($data as $value) {
            $queue->enqueue($value);
        }
        
        foreach ($tasks as $task) {
            $task->expects($this->never())
                ->method('work');
        }
        
        $this->worker->expects($this->once())
                ->method('getMsgsReceived')
                ->will($this->returnValue($queue));
        
        $this->worker->expects($this->once())
                ->method('setIsInactive');
        
        $childType = new WorkerChildType($this->worker);
        $childType->processQueueMessages();
    }
    
    public function testRun()
    {
        $this->worker->expects($this->exactly(2))
                ->method('sendMsgStillAlive');
        
        //is active is called 3 times : two times => true, last time => false
        $this->worker->expects($this->exactly(3))
                ->method('isActive')
                ->will($this->onConsecutiveCalls(true, true, false));

        $this->worker->expects($this->exactly(2))
                ->method('processQueue');
        
        $this->worker->expects($this->exactly(2))
                ->method('isInTimeOut')
                ->will($this->onConsecutiveCalls(true, false));
        
        $childType = new WorkerChildType($this->worker);
        $childType->run();
    }
}
