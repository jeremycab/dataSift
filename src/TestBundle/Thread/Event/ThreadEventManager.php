<?php

namespace DataSift\TestBundle\Thread\Event;

use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use DataSift\TestBundle\Thread\Event\Observer\ThreadEventObserverInterface;

/**
 * Description of ThreadEventManager
 *
 * @author jeremy
 */
class ThreadEventManager
{

    private $threadManager;
    private $observers;

    public function __construct(ThreadManager $threadManager)
    {
        $this->threadManager = $threadManager;
        $this->observers = array();

        $this->threadManager->onChildExit($this, 'signalHandler');
    }

    public function addEventObserver(ThreadEventObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    public function onChildExit($pid)
    {
        foreach ($this->observers as $observer) {
            $observer->onChildExit($pid);
        }
    }

    public static function signalHandler($signo)
    {
        $eventManager = $GLOBALS['threadEventManager'];
        if (!$eventManager instanceof ThreadEventManager) {
            return;
        }

        $pid = pcntl_waitpid(-1, $status, WNOHANG);

        if ($pid != 0) {
            $eventManager->onChildExit($pid);
        }
    }

}

