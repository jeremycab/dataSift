<?php

namespace DataSift\TestBundle\Server\Socket\Server;

use \DataSift\TestBundle\Log\Logger\LoggerInterface;
use \DataSift\TestBundle\Server\Listener\ServerListenerInterface;
use \DataSift\TestBundle\Server\ServerInterface;

/**
 * Description of SocketServer
 *
 * @author jcabantous
 */
class SocketServer extends SocketServerAbstract implements ServerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @var array
     */
    private $listeners;
    
    public function __construct($address, $port, $maxClients, LoggerInterface $logger)
    {
        parent::__construct($address, $port, $maxClients);
        $this->logger = $logger;
        $this->listeners = array();
    }
    
    protected function onClientConnected($socket)
    {

    }

    protected function onClientDisconnected($socket)
    {

    }

    protected function onDataReceived($socket, $data)
    {
        /* @var $listener ServerListenerInterface */
        foreach ($this->listeners as $listener) {
            $listener->onDataReceived($data);
        }
        
        socket_write($socket, 'Data sent to workers');
     //   socket_close($socket);
    }  
    
    public function log($message, $socketError = false)
    {
        if ($socketError) {
            $errNo = socket_last_error();
            $errMsg = socket_strerror($errNo);

            $message = ' : #' . $errNo . ' ' . $errMsg;
        }
        
        $this->logger->log($message);
    }
    
    public function addListener(ServerListenerInterface $listener)
    {
        $this->listeners[] = $listener;
    }
}
