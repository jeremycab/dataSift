<?php

namespace DataSift\TestBundle\Log\Logger;

/**
 * Description of FileLogger
 *
 * @author jcabantous
 */
class FileLogger implements LoggerInterface
{
    private $file;
    
    public function __construct($path)
    {
        $this->file = new \SplFileObject($path, "w+");
    }
    
    public function log($msg)
    {
        $msg = '[' . date('Y-m-d H:i:s') . '] : ' . "$msg \n";
        $this->file->fwrite($msg);
    }    
}
