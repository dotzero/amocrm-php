<?php

namespace AmoCRM\Tests\Request;

use AmoCRM\Tests\TestCase;
use AmoCRM\Request\ParamsBag;

class ParamsBagTest extends TestCase
{
    private $params = null;

    public function setUp()
    {
        $this->params = new ParamsBag();

        $this->assertInstanceOf('\AmoCRM\Request\ParamsBagInterface', $this->params);
    }

    public function testAuth()
    {
        $this->params->addAuth('key', 'value')->addAuth('foo', 'bar');

        $this->assertEquals('value', $this->params->getAuth('key'));
        $this->assertEquals('bar', $this->params->getAuth('foo'));
        $this->assertCount(2, $this->params->getAuth());
    }

    public function testGet()
    {
        $this->params->addGet('key', 'value')->addGet(['foo' => 'bar']);

        $this->assertEquals('value', $this->params->getGet('key'));
        $this->assertEquals('bar', $this->params->getGet('foo'));
        $this->assertCount(2, $this->params->getGet());

        $this->params->clearGet();
        $this->assertCount(0, $this->params->getGet());
    }

    public function testPost()
    {
        $this->params->addPost('key', 'value')->addPost(['foo' => 'bar']);

        $this->assertEquals('value', $this->params->getPost('key'));
        $this->assertEquals('bar', $this->params->getPost('foo'));
        $this->assertCount(2, $this->params->getPost());

        $this->params->clearPost();
        $this->assertCount(0, $this->params->getPost());
    }
}
