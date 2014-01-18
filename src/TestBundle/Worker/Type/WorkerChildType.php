<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

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
            if ($msg == Worker::MSG_QUIT) {
                $this->worker->setIsInactive();
                return;
            }

            foreach ($this->worker->getTasks() as $task) {
                 $this->worker->getQueueOut()->sendMsg($this->worker . ' start new task');
                $result = $task->work(array($msg));
                $this->worker->getQueueOut()->sendMsg('Message processed by ' . $this->worker . '. Result : ' . $result);
            }
        }
    }

    public function run()
    {
        $this->worker->sendMsgStillAlive();

        while ($this->worker->isActive()) {
            $this->worker->processQueue();
            if ($this->worker->isInTimeOut()) {
                $this->worker->sendMsgStillAlive();
            }
        }
    }
}

