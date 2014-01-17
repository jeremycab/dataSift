<?php

namespace DataSift\TestBundle\Worker\Type;

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
        foreach ($messages as $message) {
             $this->logger->log($message);
        }
    }

    public function run()
    {
        throw new \RuntimeException('The worker is not allowed to run in the parent process.');
    }
}
