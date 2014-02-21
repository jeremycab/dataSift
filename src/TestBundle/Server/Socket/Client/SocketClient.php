<?php

namespace DataSift\TestBundle\Server\Socket\Client;

/**
 * Description of Client
 *
 * @author jeremy
 */
class SocketClient
{
    private $adress;
    private $port;
    
    public function __construct($adress, $port)
    {
        $this->adress = $adress;
        $this->port = $port;
    }

    public function sendData($data)
    {
        $fp = stream_socket_client("tcp://" . $this->adress . ":" . $this->port, $errno, $errstr, 30);
        if (!$fp) {
            throw new \RuntimeException($errstr ($errno));
        } else {
            fwrite($fp, $data);
            $data = fread($fp, 26);
            fclose($fp);
            
            return $data;
        }
    }

}