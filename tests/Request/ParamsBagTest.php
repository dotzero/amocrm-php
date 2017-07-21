<?php

class ParamsBagTest extends TestCase
{
    private $params = null;

    public function setUp()
    {
        $this->params = new \AmoCRM\Request\ParamsBag();
    }

    public function testAuth()
    {
        $this->params->addAuth('key', 'value')->addAuth('foo', 'bar');

        $this->assertEquals('value', $this->params->getAuth('key'));
        $this->assertEquals('bar', $this->params->getAuth('foo'));
        $this->assertCount(2, $this->params->getAuth());
    }

    public function testProxy()
    {
        $this->params->addProxy('http://proxy.url');

        $this->assertTrue($this->params->hasProxy());
        $this->assertEquals('http://proxy.url', $this->params->getProxy());
    }

    public function testGet()
    {
        $this->params->addGet('key', 'value')->addGet(['foo' => 'bar']);

        $this->assertEquals('value', $this->params->getGet('key'));
        $this->assertEquals('bar', $this->params->getGet('foo'));
        $this->assertCount(2, $this->params->getGet());
        $this->assertTrue($this->params->hasGet());

        $this->params->clearGet();
        $this->assertFalse($this->params->hasGet());
    }

    public function testPost()
    {
        $this->params->addPost('key', 'value')->addPost(['foo' => 'bar']);

        $this->assertEquals('value', $this->params->getPost('key'));
        $this->assertEquals('bar', $this->params->getPost('foo'));
        $this->assertCount(2, $this->params->getPost());
        $this->assertTrue($this->params->hasPost());

        $this->params->clearPost();
        $this->assertFalse($this->params->hasPost());
    }
}
