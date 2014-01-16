<?php

namespace DataSift\TestBundle\Thread\Event\Observer;

/**
 *
 * @author jeremy
 */
interface ThreadEventObserverInterface {
    
    public function onChildExit($pid);
}

