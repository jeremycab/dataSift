<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

/**
 * strategy to use when the worker is in the parent thread
 * @author jcabantous
 */
class WorkerParentType extends WorkerAbstractType
{
    /**
     * override WorkerAbstractType
     * @return boolean
     */
    public function isInTimeOut()
    {
        //wait a little bit more to avoid delay issues
        return ((time() - $this->worker->getQueueOut()->getLastMsgReceived() ) > ($this->worker->getTimeout()) + 3);
    }

    /**
     * override WorkerAbstractType
     */
    public function processQueueMessages()
    {
        //get all the messages sent byt the worker
        $messages = $this->worker->getQueueOut()->getCurrentMsg();
        //if an inactive worker has sent a message => we ask it to quit
        if (!$this->worker->isActive() && $messages->count() > 0) {
            $this->logger->log($this->worker . ' has to quit : ');
            $this->worker->getQueueIn()->sendMsg(Worker::MSG_QUIT);
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
