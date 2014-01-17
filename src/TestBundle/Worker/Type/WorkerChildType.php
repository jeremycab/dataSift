<?php

namespace DataSift\TestBundle\Worker\Type;

/**
 * Description of WorkerTypeChild
 *
 * @author jcabantous
 */
class WorkerChildType extends WorkerAbstractType
{

    public function isInTimeOut()
    {
        return ((time() - $this->worker->getQueueOut()->getLastMsgSent()) > $this->worker->getTimeout());
    }

    public function processQueueMessages()
    {
        $queue = $this->worker->getQueueIn()->getCurrentMsg();
        foreach ($queue as $msg) {
            foreach ($this->worker->getTasks() as $task) {
                $result = $task->work(array($msg));
                $this->worker->getQueueOut()->sendMsg('Message processed by ' . $this->thread . '. Result : ' . $result);
            }
        }
    }

    public function run()
    {
        $this->worker->sendMsgStillAlive();

        while ($this->worker->isRunning()) {
            $this->worker->processQueue();
            if ($this->worker->isInTimeOut()) {
                $this->worker->sendMsgStillAlive();
            }
        }
    }
}
