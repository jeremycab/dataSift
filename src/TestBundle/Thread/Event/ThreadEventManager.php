<?php

namespace DataSift\TestBundle\Thread\Event;

use \DataSift\TestBundle\Thread\Manager\ThreadManager;
use DataSift\TestBundle\Thread\Event\Observer\ThreadEventObserverInterface;

/**
 * manage the events of the children processes
 * @author jeremy
 */
class ThreadEventManager
{
    /**
     * @var ThreadManager
     */
    private $threadManager;
    
    /**
     * list of observers to call when an event happened
     * @var array
     */
    private $observers;

    /**
     * init the event manager with the thread manager
     * @param \DataSift\TestBundle\Thread\Manager\ThreadManager $threadManager
     */
    public function __construct(ThreadManager $threadManager)
    {
        $this->threadManager = $threadManager;
        $this->observers = array();
        
        //inform the threadmanager to call the method "signalHandler" when a child stops
        $this->threadManager->onChildExit($this, 'signalHandler');
    }

    /**
     * add an observer to call when an event occurs
     * @param \DataSift\TestBundle\Thread\Event\Observer\ThreadEventObserverInterface $observer
     */
    public function addEventObserver(ThreadEventObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * warn the observers that a child is dead
     * @param int $pid : the process ID killed
     */
    public function onChildExit($pid)
    {
        foreach ($this->observers as $observer) {
            $observer->onChildExit($pid);
        }
    }

    /**
     * function called when en event occurs
     * @param int $signo
     */
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

