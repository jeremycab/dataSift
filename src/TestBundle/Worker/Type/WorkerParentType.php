<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

/**
 * strategy to use when the worker is in the parent thread
 * @author jcabantous
 */
class WorkerParentType extends WorkerAbstractType
{
    const DELAY_TIMEOUT = 3;
    
    /**
     * override WorkerAbstractType
     * @return boolean
     */
    public function isInTimeOut()
    {
        //wait a little bit more to avoid delay issues
        return ((time() - $this->worker->getTimestampLastMsgReceived() ) > ($this->worker->getTimeout()) + self::DELAY_TIMEOUT);
    }

    /**
     * override WorkerAbstractType
     */
    public function processQueueMessages()
    {
        //get all the messages sent byt the worker
        $messages = $this->worker->getMsgsSent();
        //if an inactive worker has sent a message => we ask it to quit
        if (!$this->worker->isActive() && $messages->count() > 0) {
            $this->logger->log($this->worker . ' has to quit : ');
            $this->worker->sendMsgTo(Worker::MSG_QUIT);
        }
        
        foreach ($messages as $message) {
             $this->logger->log($message);
        }
    }

    /**
     * override WorkerAbstractType
     * @throws \RuntimeException
     */
    public function run()
    {
        //the work must not be run in the parent thread
        throw new \RuntimeException('The worker is not allowed to run in the parent process.');
    }
}
