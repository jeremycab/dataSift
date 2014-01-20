<?php

namespace DataSift\TestBundle\Tests\Worker\Type;

use \DataSift\TestBundle\Worker\Type\WorkerFactoryType;

/**
 * Description of WorkerFactoryTypeTest
 *
 * @author jcabantous
 */
class WorkerFactoryTypeTest extends \PHPUnit_Framework_TestCase
{
    private $worker;
    
    public function setUp()
    {
        $this->worker = $this->getMockBuilder('\DataSift\TestBundle\Worker\Worker')
                ->disableOriginalConstructor()
                ->getMock();
    }
    
    public function testGetTypeParent()
    {
        $factory = new WorkerFactoryType();
        $parent = $factory->getTypeParent($this->worker);
        
        $this->assertInstanceOf('\DataSift\TestBundle\Worker\Type\WorkerParentType', $parent);
    }
    
    public function testGetTypeChild()
    {
        $factory = new WorkerFactoryType();
        $parent = $factory->getTypeChild($this->worker);
        
        $this->assertInstanceOf('\DataSift\TestBundle\Worker\Type\WorkerChildType', $parent);
    }
}
