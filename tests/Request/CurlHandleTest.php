<?php

class CurlHandleTest extends TestCase
{
    /** @var \AmoCRM\Request\CurlHandle */
    private $handle;

    public function setUp()
    {
        $this->handle = new \AmoCRM\Request\CurlHandle();
    }

    public function testOpen()
    {
        $ch = $this->handle->open();
        $this->assertInternalType('resource', $ch);
    }

    public function testOpenWillReturnTheSameHandle()
    {
        $ch1 = $this->handle->open();
        $this->handle->close();
        $ch2 = $this->handle->open();
        $this->assertEquals($ch1, $ch2);
    }

    public function testClose()
    {
        $ch = $this->handle->open();
        $this->handle->close();
    }

    public function testCloseWithoutOpen()
    {
        $this->handle->close();
    }
}
