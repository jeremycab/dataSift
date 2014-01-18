<?php

namespace DataSift\TestBundle\Socket\Client;

/**
 * Description of Client
 *
 * @author jeremy
 */
class SocketClient
{

    public function sendData($data)
    {
        $fp = stream_socket_client("tcp://127.0.0.1:8080", $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            fwrite($fp, $data);
            $data = fread($fp, 26);
            fclose($fp);
            
            return $data;
        }
    }

}