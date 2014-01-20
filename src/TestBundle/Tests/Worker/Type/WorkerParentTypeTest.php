<?php

namespace DataSift\TestBundle\Tests\Worker\Type;

use \DataSift\TestBundle\Worker\Type\WorkerParentType;
use \DataSift\TestBundle\Worker\Worker;

/**
 * Description of WorkerParentTypeTest
 *
 * @author jcabantous
 */
class WorkerParentTypeTest extends \PHPUnit_Framework_TestCase
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
                ->method('getTimestampLastMsgReceived')
                ->will($this->returnValue(time() - WorkerParentType::DELAY_TIMEOUT - 6));

        $parentType = new WorkerParentType($this->worker);
        $result = $parentType->isInTimeOut();

        $this->assertTrue($result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRun()
    {
        $parentType = new WorkerParentType($this->worker);
        $parentType->run();
    }

    public function providerMessagesWorkerActive()
    {
        return array(
            array(
                'data' => array(),
            ),
            array(
                'data' => array(1),
            ),
            array(
                'data' => array(1, 2),
            ),
            array(
                'data' => array(1, 2, 3, 4, 5, 6, 8),
            ),
        );
    }

    /**
     * @dataProvider providerMessagesWorkerActive
     * @param type $messages
     */
    public function testProcessQueueMessagesWhenWorkerIsActive($messages)
    {
        $queue = new \SplQueue();
        foreach ($messages as $message) {
            $queue->enqueue($message);
        }

        $this->worker->expects($this->once())
                ->method('getMsgsSent')
                ->will($this->returnValue($queue));

        $this->worker->expects($this->any())
                ->method('isActive')
                ->will($this->returnValue(true));

        $this->worker->expects($this->never())
                ->method('sendMsgTo');

        $this->logger->expects($this->exactly(count($messages)))
                ->method('log');

        $parentType = new WorkerParentType($this->worker);
        $parentType->processQueueMessages();
    }
    
    public function providerMessagesWorkerInactive()
    {
        return array(
            array(
                'data' => array(1),
            ),
            array(
                'data' => array(1, 2),
            ),
            array(
                'data' => array(1, 2, 3, 4, 5, 6, 8),
            ),
        );
    }

    /**
     * @dataProvider providerMessagesWorkerInactive
     * @param type $messages
     */
    public function testProcessQueueMessagesWhenWorkerIsInactive($messages)
    {
        $queue = new \SplQueue();
        foreach ($messages as $message) {
            $queue->enqueue($message);
        }

        $this->worker->expects($this->once())
                ->method('getMsgsSent')
                ->will($this->returnValue($queue));

        $this->worker->expects($this->any())
                ->method('isActive')
                ->will($this->returnValue(false));

        $this->worker->expects($this->once())
                ->method('sendMsgTo')
                ->with(Worker::MSG_QUIT);

        $this->logger->expects($this->exactly(count($messages) + 1))
                ->method('log');

        $parentType = new WorkerParentType($this->worker);
        $parentType->processQueueMessages();
    }
    
    public function testProcessQueueWheWorkerInactiveAndEmptyQueue()
    {
        $queue = new \SplQueue();
        $this->worker->expects($this->once())
                ->method('getMsgsSent')
                ->will($this->returnValue($queue));

        $this->worker->expects($this->any())
                ->method('isActive')
                ->will($this->returnValue(false));

        $this->worker->expects($this->never())
                ->method('sendMsgTo');

        $this->logger->expects($this->never())
                ->method('log');

        $parentType = new WorkerParentType($this->worker);
        $parentType->processQueueMessages();
    }
}
