<?php

namespace DataSift\TestBundle\Tests\Worker;

use \DataSift\TestBundle\Worker\Worker;

/**
 * Description of WorkerTest
 *
 * @author jcabantous
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{
    const TIMEOUT = 10;
    
    private $queueIn;
    private $queueOut;
    private $thread;
    private $typeFactory;
    private $logger;
    private $typeWorker;
        
    public function setUp()
    {
        $this->queueIn = $this->getMockBuilder('DataSift\TestBundle\Queue\QueueManager')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->queueOut = $this->getMockBuilder('DataSift\TestBundle\Queue\QueueManager')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->thread = $this->getMockBuilder('\DataSift\TestBundle\Thread\Thread')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->typeFactory = $this->getMockBuilder('DataSift\TestBundle\Worker\Type\WorkerFactoryType')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->logger = $this->getMockBuilder('\DataSift\TestBundle\Log\Logger\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->typeWorker = $this->getMockBuilder('DataSift\TestBundle\Worker\Type\WorkerAbstractType')
                ->disableOriginalConstructor()
                ->getMock();
        
        $this->typeFactory->expects($this->any())
                ->method('getTypeParent')
                ->will($this->returnValue($this->typeWorker));
    }
    
    public function testQueueIn()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $this->assertSame($this->queueIn, $worker->getQueueIn());
    }
    
    public function testQueueOut()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $this->assertSame($this->queueOut, $worker->getQueueOut());
    }
    
    public function testLogger()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $this->assertSame($this->logger, $worker->getLogger());
    }
    
    public function testGetThread()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $this->assertSame($this->thread, $worker->getThread());
    }
    
    public function testSendMsgTo()
    {
        $msg = 'ezrzerzer';
        $this->queueIn->expects($this->once())
                ->method('sendMsg')
                ->with($msg);
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $worker->sendMsgTo($msg);
    }
    
    public function testSendMsgFrom()
    {
        $msg = 'ezrzerzer';
        $this->queueOut->expects($this->once())
                ->method('sendMsg')
                ->with($msg);
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $worker->sendMsgFrom($msg);
    }
    
    public function testSendStillAlive()
    {
        $this->queueOut->expects($this->once())
                ->method('sendMsg');
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $worker->sendMsgStillAlive();
    }
    
    public function testProcessQueue()
    {
        $this->typeWorker->expects($this->once())
                ->method('processQueueMessages');
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $worker->processQueue();
    }
    
    public function testRun()
    {
        $this->typeWorker->expects($this->once())
                ->method('run');
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $worker->run();
    }
    
    public function testIsInTimeout()
    {
        $value = 'dummyreturnvalue';
        $this->typeWorker->expects($this->once())
                ->method('isInTimeOut')
                ->will($this->returnValue($value));
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $return = $worker->isInTimeOut();
        
        $this->assertEquals($return, $value);
    }
    
    public function testSetIsInChildProcess()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        
        $this->typeFactory->expects($this->once())
                ->method('getTypeChild')
                ->with($worker);
        
        $worker->setIsInChildProcess();
    }
    
    
    public function testActive()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $this->assertTrue($worker->isActive());
        
        $worker->setIsInactive();
        $this->assertFalse($worker->isActive());
        
        $worker->setIsActive();
        $this->assertTrue($worker->isActive());
    }
    
    public function testToString()
    {
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $this->assertTrue(is_string('erzerezeer : ' . $worker));
    }
    
    public function testGetTimestampLastMsgReceived()
    {
        $dummyReturn = rand(0, 1000);
        
        $this->queueOut->expects($this->once())
                ->method('getLastMsgReceived')
                ->will($this->returnValue($dummyReturn));
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $return = $worker->getTimestampLastMsgReceived();
        
        $this->assertEquals($dummyReturn, $return);
    }
    
    public function testGetTimestampLastMsgSent()
    {
        $dummyReturn = rand(0, 1000);
        
        $this->queueOut->expects($this->once())
                ->method('getLastMsgSent')
                ->will($this->returnValue($dummyReturn));
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $return = $worker->getTimestampLastMsgSent();
        
        $this->assertEquals($dummyReturn, $return);
    }
    
    public function testGetMsgsSent()
    {
        $dummyReturn = rand(0, 1000);
        
        $this->queueOut->expects($this->once())
                ->method('getCurrentMsg')
                ->will($this->returnValue($dummyReturn));
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $return = $worker->getMsgsSent();
        
        $this->assertEquals($dummyReturn, $return);
    }
    
    public function testGetMsgsReceived()
    {
        $dummyReturn = rand(0, 1000);
        
        $this->queueIn->expects($this->once())
                ->method('getCurrentMsg')
                ->will($this->returnValue($dummyReturn));
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $return = $worker->getMsgsReceived();
        
        $this->assertEquals($dummyReturn, $return);
    }
    
    public function testGetAndSetTasks()
    {
        $task = $this->getMockBuilder('\DataSift\TestBundle\Task\TaskInterface')
                ->disableOriginalConstructor()
                ->getMock();
        
        $worker = new Worker($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT);
        $worker->addTask($task);
        $this->assertCount(1, $worker->getTasks());
        
        $worker->addTask($task);
        $this->assertCount(2, $worker->getTasks());
    }
    
    
    public function providerTestIsAvailable()
    {
        return array(
            array(
                'nbMessagesInQueue' => 2,
                'isInTimeout' => false,
                'isActive' => true,
                'expected' => false,
            ),
            array(
                'nbMessagesInQueue' => 0,
                'isInTimeout' => true,
                'isActive' => true,
                'expected' => false,
            ),
            array(
                'nbMessagesInQueue' => 0,
                'isInTimeout' => false,
                'isActive' => false,
                'expected' => false,
            ),
            array(
                'nbMessagesInQueue' => 0,
                'isInTimeout' => false,
                'isActive' => true,
                'expected' => true,
            ),
        );
    }
    
    /**
     * @dataProvider providerTestIsAvailable
     * @param type $nbMessagesInQueue
     * @param type $isInTimeout
     * @param type $isActive
     * @param type $expected
     */
    public function testIsAvailable($nbMessagesInQueue, $isInTimeout, $isActive, $expected)
    {
        $worker = $this->getMock(
                '\DataSift\TestBundle\Worker\Worker', 
                array('isInTimeOut'), 
                array($this->thread, $this->queueIn, $this->queueOut, $this->typeFactory, $this->logger, self::TIMEOUT));
        
        $this->queueIn->expects($this->any())
                ->method('countNbMessagesInQueue')
                ->will($this->returnValue($nbMessagesInQueue));
        
        $worker->expects($this->any())
                ->method('isInTimeOut')
                ->will($this->returnValue($isInTimeout));
        
        if ($isActive) {
            $worker->setIsActive();
        } else {
            $worker->setIsInactive();
        }
        
        $result = $worker->isAvailable();
        $this->assertEquals($expected, $result);
    }
}
