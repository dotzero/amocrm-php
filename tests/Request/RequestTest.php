<?php

class RequestTest extends PHPUnit_Framework_TestCase
{
    private $request = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->request = new \AmoCRM\Request\Request($paramsBag);
    }

    public function testConstructor()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Request\Request')
            ->setConstructorArgs([new \AmoCRM\Request\ParamsBag()])
            ->setMethods(['getRequest'])
            ->getMock();

        $this->assertInstanceOf('\AmoCRM\Request\Request', $mock);
    }
}
