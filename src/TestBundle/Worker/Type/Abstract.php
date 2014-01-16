<?php

/**
 * Description of Abstract
 *
 * @author jeremy
 */
abstract class WorkerAbstractType {
    
    abstract public function processQueueMessages();
    
    abstract public function isInTimeOut();
}
