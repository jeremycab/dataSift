<?php

namespace DataSift\TestBundle\Tests\Worker\Manager;

use \DataSift\TestBundle\Worker\Manager\WorkerManager;

/**
 * Description of WorkerManagerTest
 *
 * @author jcabantous
 */
class WorkerManagerTest extends \PHPUnit_Framework_TestCase
{
    private $threadManager;
    private $logger;
    private $workerFactory;
    private $worker;
    private $thread;
    private $workersCollection;

    public function setUp()
    {
        $this->threadManager = $this->getMockBuilder('\DataSift\TestBundle\Thread\Manager\ThreadManager')
                ->disableOriginalConstructor()
                ->getMock();

        $this->logger = $this->getMockBuilder('\DataSift\TestBundle\Log\Logger\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock();

        $this->workerFactory = $this->getMockBuilder('\DataSift\TestBundle\Worker\Factory\WorkerFactory')
                ->disableOriginalConstructor()
                ->getMock();

        $this->worker = $this->getMockBuilder('\DataSift\TestBundle\Worker\Worker')
                ->disableOriginalConstructor()
                ->getMock();

        $this->thread = $this->getMockBuilder('DataSift\TestBundle\Thread\Thread')
                ->disableOriginalConstructor()
                ->getMock();

        $this->workersCollection = $this->getMockBuilder('DataSift\TestBundle\Worker\Collection\WorkerCollection')
                ->disableOriginalConstructor()
                ->getMock();

        //bind the thread to the worker
        $this->worker->expects($this->any())
                ->method('getThread')
                ->will($this->returnValue($this->thread));
    }

    public function testLaunchWorkerInParentThread()
    {
        $pid = 2;
        $this->threadManager->expects($this->once())
                ->method('fork')
                ->will($this->returnValue($pid));

        $this->thread->expects($this->once())
                ->method('setPid')
                ->with($pid);

        $this->worker->expects($this->once())
                ->method('setIsActive');

        $this->workersCollection->expects($this->once())
                ->method('addWorker')
                ->with($this->worker);

        $workerManager = new WorkerManager($this->threadManager, $this->workerFactory, $this->logger,
                                           $this->workersCollection);
        $workerManager->launchWorker($this->worker);
    }

    public function testLaunchWorkerInChildThread()
    {
        $pid = 0;
        $this->threadManager->expects($this->once())
                ->method('fork')
                ->will($this->returnValue($pid));

        $this->worker->expects($this->once())
                ->method('setIsInChildProcess');

        $this->worker->expects($this->once())
                ->method('run');

        $this->thread->expects($this->once())
                ->method('loadCurrentPid');

        $this->worker->expects($this->once())
                ->method('stop');

        $workerManager = new WorkerManager($this->threadManager, $this->workerFactory, $this->logger,
                                           $this->workersCollection);
        $workerManager->launchWorker($this->worker);
    }

    public function testCheckWorkersStatus()
    {
        $this->worker->expects($this->exactly(4))
                ->method('processQueue');

        $this->thread->expects($this->once())
                ->method('getPid')
                ->will($this->returnValue(4));

        $this->worker->expects($this->any())
                ->method('isActive')
                ->will($this->onConsecutiveCalls(false, true, false, true));

        $this->worker->expects($this->any())
                ->method('isInTimeOut')
                ->will($this->onConsecutiveCalls(true, false, false, true));

        $this->workersCollection->expects($this->any())
                ->method('getAll')
                ->will($this->returnValue(array($this->worker, $this->worker, $this->worker, $this->worker)));

        $workerManager = $this->getMock('\DataSift\TestBundle\Worker\Manager\WorkerManager', array('onChildExit'),
                                        array(
            $this->threadManager,
            $this->workerFactory,
            $this->logger,
            $this->workersCollection));
        
        $workerManager->expects($this->once())
                ->method('onChildExit')
                ->with(4);
        $workerManager->checkWorkersStatus();
    }

    public function testDispatchDataToWorkers()
    {
        $data = array('test 1', 'test 2', 'test 3');

        $this->worker->expects($this->exactly(4))
                ->method('isAvailable')
                ->will($this->onConsecutiveCalls(false, true, true, true));

        $this->worker->expects($this->exactly(3))
                ->method('sendMsgTo');

        $this->workersCollection->expects($this->any())
                ->method('getAll')
                ->will($this->returnValue(array($this->worker, $this->worker, $this->worker, $this->worker)));

        $workerManager = new WorkerManager($this->threadManager, $this->workerFactory, $this->logger,
                                           $this->workersCollection);

        foreach ($data as $value) {
            $workerManager->onDataReceived($value);
        }

        //launch the test several times
        $workerManager->dispatchDataToWorkers();
        $workerManager->dispatchDataToWorkers();
        $workerManager->dispatchDataToWorkers();
    }

    public function testOnChildExit()
    {
        $this->workersCollection->expects($this->any())
                ->method('getWorkerFromPid')
                ->will($this->onConsecutiveCalls($this->worker, $this->worker));

        $this->worker->expects($this->any())
                ->method('isActive')
                ->will($this->onConsecutiveCalls(true, FALSE));

        $this->worker->expects($this->exactly(1))
                ->method('setIsInactive');

        $this->worker->expects($this->exactly(1))
                ->method('getMsgsReceived')
                ->will($this->returnValue(array()));

        $newWorker = $this->getMockBuilder('\DataSift\TestBundle\Worker\Worker')
                ->disableOriginalConstructor()
                ->getMock();

        $this->workerFactory->expects($this->once())
                ->method('copyWorker')
                ->will($this->returnValue($newWorker));

        $workerManager = $this->getMock('\DataSift\TestBundle\Worker\Manager\WorkerManager', array('launchWorker'),
                                        array($this->threadManager, $this->workerFactory, $this->logger, $this->workersCollection));

        $workerManager->expects($this->once())
                ->method('launchWorker')
                ->with($this->worker);

        $workerManager->onChildExit(24);
        $workerManager->onChildExit(78);
    }
}
