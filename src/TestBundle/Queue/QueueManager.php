<?php

namespace DataSift\TestBundle\Queue;

/**
 * Manage a speicific queue 
 * @author jcabantous
 */
class QueueManager
{

    /**
     * the queue to manage
     * @var Queue
     */
    private $queue;

    /**
     * init the queue to manage
     * @param \DataSift\TestBundle\Queue\Queue $queue
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * get all the messages of the queue to process
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

    /**
     * send a message into the queue
     * @param mixed $msg
     */
    public function sendMsg($msg)
    {
        $this->queue->sendMsg($msg);
    }

    /**
     * count how many messages are waiting in the queue
     * @return int
     */
    public function countNbMessagesInQueue()
    {
        $stats = $this->queue->getStats();
        return $stats['msg_qnum'];
    }

    /**
     * return the unix timestamp of the last message sent in the queue
     * @return int
     */
    public function getLastMsgSent()
    {
        $stats = $this->queue->getStats();
        return $stats['msg_stime'];
    }

    /**
     * return the unix timestamp of the last message received in the queue
     * @return int
     */
    public function getLastMsgReceived()
    {
        $stats = $this->queue->getStats();
        return $stats['msg_rtime'];
    }
}
