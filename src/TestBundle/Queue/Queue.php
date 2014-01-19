<?php

namespace DataSift\TestBundle\Queue;

/**
 * Manage PHP queue functions
 * @author jcabantous
 */
class Queue
{
    const MAX_SIZE = 1024;
    
    /**
     * id of the queue managed
     * @var int 
     */
    private $id;
    
    /**
     * PHP queue ressource
     * @var ressource
     */
    private $queueRessource;
    
    /**
     * load a new queue tu manage
     */
    public function __construct()
    {
        $id = rand(1, 10000000);
        while (msg_queue_exists($id)) {
            $id = rand(1, 10000000);
        }
        $this->id = $id;
        $this->loadQueueRessource();
    }
    
    /**
     * return all the messages not processed 
     * @return array
     */
    public function getMsgQueue()
    {
        $messages = array();
        $stats = $this->getStats();

        for ($i =0; $i < $stats['msg_qnum']; $i++) {
            msg_receive($this->queueRessource, 0, $msgType, self::MAX_SIZE, $message);
           $messages[] = $message;
        }
        
        return $messages;
    }
    
    /**
     * send a message into the queue
     * @param mixed $msg
     */
    public function sendMsg($msg)
    {
        msg_send($this->queueRessource, 1, $msg);
    }
    
    /**
     * get an array of statistics of the queue
     * @return array
     */
    public function getStats()
    {
        return msg_stat_queue($this->queueRessource);
    }
    
    /**
     * load the PHP queue ressource of the current queue
     */
    private function loadQueueRessource()
    {
        $this->queueRessource = msg_get_queue($this->id);
    }
}
