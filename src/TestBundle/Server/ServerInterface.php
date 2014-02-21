<?php

namespace DataSift\TestBundle\Server;

use DataSift\TestBundle\Server\Listener\ServerListenerInterface;

/**
 *
 * @author jeremy
 */
interface ServerInterface
{
    /**
     * start the server
     */
    public function start();
    
    /**
     * add a listener to the server
     * @param \DataSift\TestBundle\Server\Listener\ServerListenerInterface $listener
     */
    public function addListener(ServerListenerInterface $listener);
    
    /**
     * listen operation
     */
    public function listen();
}

