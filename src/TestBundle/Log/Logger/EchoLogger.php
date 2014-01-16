<?php

namespace DataSift\TestBundle\Log\Logger;

/**
 * Description of EchoLogger
 *
 * @author jcabantous
 */
class EchoLogger implements LoggerInterface
{
    public function log($msg)
    {
        echo "$msg \n";
    }    
}
