<?php

namespace DataSift\TestBundle\Queue;

/**
 * Description of Queue
 *
 * @author jcabantous
 */
class QueueManager
{
    private $queue;
    
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }
    
    /**
     * 
     * @return \SplQueue
     */
    public function getCurrentMsg()
    {
        $queueSpl = new \SplQueue();
        $msgs = $this->queue->getMsgQueue();

        if (!empty($msgs)) {
            foreach ($msgs as $msg) {
                $queueSpl->enqueue($msg);
            }
        }
        
        return $queueSpl;
    }
    
    public function sendMsg($msg)
    {
        $this->queue->sendMsg($msg);
    }
    
    public function clear()
    {
        $this->queue->removeQueue();
    }
    
    public function countNbMessagesInQueue()
    {
        return $this->queue->countMsgInQueue();
    }
}
