<?php

namespace DataSift\TestBundle\Worker\Type;

use \DataSift\TestBundle\Worker\Worker;

/**
 * strategy to use when the worker is call in the child thread
 * @author jcabantous
 */
class WorkerChildType extends WorkerAbstractType
{
    /**
     * override WorkerAbstractType
     * @return boolean
     */
    public function isInTimeOut()
    {
        return ((time() - $this->worker->getTimestampLastMsgSent()) > $this->worker->getTimeout());
    }

    /**
     * override WorkerAbstractType
     */
    public function processQueueMessages()
    {
        //get all the messages to process
        $queue = $this->worker->getMsgsReceived();

        //we check for each of them if it's a message saying that we have to stop the worker
        foreach ($queue as $msg) {
            if ($msg == Worker::MSG_QUIT) {
                $this->worker->setIsInactive();
                return;
            }

            $tasks = $this->worker->getTasks();
            //call each task with the data received
            foreach ($tasks as $task) {
                $this->worker->sendMsgFrom($this->worker . ' start new task');
                $result = $task->work($msg);
                $this->worker->sendMsgFrom('Message processed by ' . $this->worker . '. Result : ' . $result);
            }
        }
    }

    /**
     * override WorkerAbstractType
     */
    public function run()
    {
        //start to send a message to the worker manager that everything is ok
        $this->worker->sendMsgStillAlive();

        //while the worker has to be active
        while ($this->worker->isActive()) {
            //process the queue
            $this->worker->processQueue();
            //and send a message if the worker is in timeout
            if ($this->worker->isInTimeOut()) {
                $this->worker->sendMsgStillAlive();
            }
        }
    }
}

