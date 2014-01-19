<?php

namespace DataSift\TestBundle\Log\Logger;

/**
 * dummy interface of a logger
 * @author jcabantous
 */
interface LoggerInterface
{
    /**
     * log a specific message
     * @param string $msg
     */
    public function log($msg);
}
