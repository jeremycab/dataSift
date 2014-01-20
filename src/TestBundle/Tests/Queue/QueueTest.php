<?php

namespace DataSift\TestBundle\Tests\Queue;

use DataSift\TestBundle\Queue\Queue;

/**
 * Description of QueueTest
 *
 * @author jcabantous
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{
    public function providertestSendAndReceiveMsgs()
    {
        return array(
            array(
                'data' => array()
            ),
            array(
                'data' => array(1)
            ),
            array(
                'data' => array(1,2,3,5,9,8)
            ),
        );
    }
    
    /**
     * @dataProvider providertestSendAndReceiveMsgs
     * @param array $msgs
     */
    public function testSendAndReceiveMsgs(array $msgs)
    {
        $queue = new Queue();
        
        foreach ($msgs as $msg) {
            $queue->sendMsg($msg);
        }
        
        $msgsReceived = $queue->getMsgQueue();
        $this->assertTrue(is_array($msgsReceived));
        $this->assertEquals($msgsReceived, $msgs);
    }
    
    public function testGetStats()
    {
        $queue = new Queue();
        $stats = $queue->getStats();
        
        $this->assertTrue(is_array($stats));
        $this->assertTrue(in_array('msg_rtime', $stats));
        $this->assertTrue(in_array('msg_stime', $stats));
        $this->assertTrue(in_array('msg_qnum', $stats));
    }
    
    
}
