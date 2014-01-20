<?php

namespace DataSift\TestBundle\Tests\Worker\Collection;

use \DataSift\TestBundle\Worker\Collection\WorkerCollection;

/**
 * Description of WorkerCollectionTest
 *
 * @author jcabantous
 */
class WorkerCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $pid = 111111;
        
        $worker = $this->getMockBuilder('\DataSift\TestBundle\Worker\Worker')
                ->disableOriginalConstructor()
                ->getMock();
        
        $thread = $this->getMockBuilder('\DataSift\TestBundle\Thread\Thread')
                ->disableOriginalConstructor()
                ->getMock();
        
        $worker->expects($this->any())
                ->method('getThread')
                ->will($this->returnValue($thread));
        
        $thread->expects($this->any())
                ->method('getPid')
                ->will($this->returnValue($pid));
        
        $collection = new WorkerCollection();
        $this->assertEmpty($collection->getAll());
        
        $collection->addWorker($worker);
        $this->assertCount(1, $collection->getAll());
        
        $worker1 = $collection->getWorkerFromPid($pid);
        $this->assertSame($worker, $worker1);
    }
}
