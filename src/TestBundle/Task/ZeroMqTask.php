<?php

namespace DataSift\TestBundle\Task;

/**
 * push data To a zeroMQ socket
 * @author jeremy
 */
class ZeroMqTask implements TaskInterface
{
    /**
     * @var \ZMQSockeT 
     */
    private $zeroMqSocket;

    public function __construct(\ZMQSockeT $zeroMq)
    {
        $this->zeroMqSocket = $zeroMq;
        $this->zeroMqSocket->connect("tcp://127.0.0.1:5555");
    }

    public function work($data)
    {
        try {
            
            $this->zeroMqSocket->send($data);
        } catch (\Exception $e) {
            return 'tot';
        }
        
        return $data;
    }

    public function getName()
    {
        return 'zeroMq';
    }

}