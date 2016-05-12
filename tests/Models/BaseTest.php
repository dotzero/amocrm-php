<?php

class BaseMock extends \AmoCRM\Models\Base
{
    protected $fields = [
        'foo',
        'bar',
    ];
}

class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|BaseMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new BaseMock($paramsBag);
    }

    public function testOffsetExists()
    {
        $this->model['foo'] = 'foobar';
        $this->assertTrue(isset($this->model['foo']));
    }

    public function testOffsetGet()
    {
        $this->model['foo'] = 'foobar';
        $this->assertEquals('foobar', $this->model['foo']);
        $this->assertEquals(null, $this->model['bar']);
    }

    public function testOffsetSet()
    {
        $this->model['foo'] = 'foobar';
        $this->assertEquals('foobar', $this->model['foo']);
    }

    public function testOffsetUncet()
    {
        $this->model['foo'] = 'foobar';
        $this->assertEquals('foobar', $this->model['foo']);
        unset($this->model['foo']);
        $this->assertEquals(null, $this->model['foo']);
    }

    public function testGetValues()
    {
        $this->assertCount(0, $this->model->getValues());
        $this->model['foo'] = 'foobar';
        $this->assertCount(1, $this->model->getValues());
    }
}
