<?php

namespace DataSift\TestBundle\Queue;

/**
 * Description of Queue
 *
 * @author jcabantous
 */
class Queue
{
    const MAX_SIZE = 1024;
    
    private $id;
    private $queueRessource;
    
    public function __construct()
    {
        $id = rand(1, 10000000);
        while (msg_queue_exists($id)) {
            $id = rand(1, 10000000);
        }
        
        $this->id = $id;
        $this->loadQueueRessource();
    }
    
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
    
    public function sendMsg($msg)
    {
        msg_send($this->queueRessource, 1, $msg);
    }
    
    public function getStats()
    {
        return msg_stat_queue($this->queueRessource);
    }
    
    private function loadQueueRessource()
    {
        $this->queueRessource = msg_get_queue($this->id);
    }
}
