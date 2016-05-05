<?php

class AccountTest extends PHPUnit_Framework_TestCase
{
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new \AmoCRM\Models\Account($paramsBag);
    }

    public function testApiCurrent()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Models\Account')
            ->setConstructorArgs([new \AmoCRM\Request\ParamsBag()])
            ->setMethods(['apiCurrent'])
            ->getMock();

        $this->assertInstanceOf('\AmoCRM\Models\Account', $mock);

        $mock->expects($this->atLeastOnce())->method('apiCurrent')
            ->will($this->returnValue([]));

        $this->assertEquals([], $mock->apiCurrent());
        $this->assertEquals([], $mock->apiCurrent(true));
    }
}
