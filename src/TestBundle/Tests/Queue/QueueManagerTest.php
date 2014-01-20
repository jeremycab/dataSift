<?php

namespace DataSift\TestBundle\Tests\Queue;

use \DataSift\TestBundle\Queue\QueueManager;

/**
 * Description of QueueManagerTest
 *
 * @author jcabantous
 */
class QueueManagerTest extends \PHPUnit_Framework_TestCase
{
    private $queue;
    
    public function setUp()
    {
        $this->queue = $this->getMockBuilder('DataSift\TestBundle\Queue\Queue')
                ->disableOriginalConstructor()
                ->getMock();
    }
    
    public function testSendMsg()
    {
        $msg = 'dqsdmodkqsdkpqsodpoqsdq';
        
        $this->queue->expects($this->once())
                ->method('sendMsg')
                ->with($msg);
        
        $queueManager = new QueueManager($this->queue);
        $queueManager->sendMsg($msg);
    }
    
    /**
     * provide the set of data to use for the test testGetCurrentMsg
     * @return type
     */
    public function providerTestGetCurrentMsg()
    {
        return array(
          array(
              'data' => array(1,2,3,4)
          ),
          array(
              'data' => array()
          ),
          array(
              'data' => array(1)
          ),
        );
    }
    
    /**
     * @dataProvider providerTestGetCurrentMsg
     * @param type $data
     */
    public function testGetCurrentMsg($data)
    {
        $this->queue->expects($this->once())
                ->method('getMsgQueue')
                ->will($this->returnValue($data));
        
        $queueManager = new QueueManager($this->queue);
        $sqlQueue = $queueManager->getCurrentMsg();
        
        $this->assertInstanceOf('\SplQueue', $sqlQueue);
        $this->assertCount(count($data), $sqlQueue);
        
        foreach ($sqlQueue as $msg) {
            $this->assertTrue(in_array($msg, $data));
        }
    }
    
    public function testCountNbMessagesInQueue()
    {
        $stats = array('msg_qnum' => 31);
        
        $this->queue->expects($this->once())
                ->method('getStats')
                ->will($this->returnValue($stats));
        
        $queueManager = new QueueManager($this->queue);
        $result = $queueManager->countNbMessagesInQueue();
        
        $this->assertEquals(31, $result);
    }
    
    public function testGetLastMsgSent()
    {
        $stats = array('msg_stime' => 216546465454);
        
        $this->queue->expects($this->once())
                ->method('getStats')
                ->will($this->returnValue($stats));
        
        $queueManager = new QueueManager($this->queue);
        $result = $queueManager->getLastMsgSent();
        
        $this->assertEquals(216546465454, $result);
    }
    
    public function testGetLastMsgReceived()
    {
        $stats = array('msg_rtime' => 7887878787);
        
        $this->queue->expects($this->once())
                ->method('getStats')
                ->will($this->returnValue($stats));
        
        $queueManager = new QueueManager($this->queue);
        $result = $queueManager->getLastMsgReceived();
        
        $this->assertEquals(7887878787, $result);
    }
}
