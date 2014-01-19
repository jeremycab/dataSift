<?php

namespace DataSift\TestBundle\Thread\Event\Observer;

/**
 * represent an observer to inform when a thread event occurs
 * @author jeremy
 */
interface ThreadEventObserverInterface
{
    /**
     * function called when a child is dead
     * @param int $pid
     */
    public function onChildExit($pid);
}

