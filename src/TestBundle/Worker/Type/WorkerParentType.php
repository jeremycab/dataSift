<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

/**
 * Description of WorkerTypeParent
 *
 * @author jcabantous
 */
class WorkerParentType extends WorkerAbstractType
{

    public function isInTimeOut()
    {
        return ((time() - $this->worker->getQueueOut()->getLastMsgReceived() ) > ($this->worker->getTimeout()) + 10);
    }

    public function processQueueMessages()
    {
        $messages = $this->worker->getQueueOut()->getCurrentMsg();
        if (!$this->worker->isActive() && $messages->count() > 0) {
            $this->logger->log($this->worker . ' has to quit : ');
            $this->worker->getQueueIn()->sendMsg(Worker::MSG_QUIT);
        }
        
        foreach ($messages as $message) {
             $this->logger->log($message);
        }
    }

    public function run()
    {
        throw new \RuntimeException('The worker is not allowed to run in the parent process.');
    }
}
