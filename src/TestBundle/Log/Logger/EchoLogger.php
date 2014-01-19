<?php

namespace DataSift\TestBundle\Log\Logger;

/**
 * make an output of each message to log
 * @author jcabantous
 */
class EchoLogger implements LoggerInterface
{
    public function log($msg)
    {
        echo '[' . date('Y-m-d H:i:s') . '] : ' . "$msg \n";
    }    
}
