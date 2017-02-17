<?php

class FieldsTest extends TestCase
{
    /**
     * @var null|\AmoCRM\Helpers\Fields
     */
    private $fields = null;

    public function setUp()
    {
        $this->fields = new \AmoCRM\Helpers\Fields();

        $this->fields->add('key1', 'value1');
        $this->fields->add('key2', 'value2');
    }

    public function testAdd()
    {
        $this->fields['key3'] = 'value3';
        $this->fields->key4 = 'value4';
        $this->fields->add('key5', 'value5');
        $this->fields->offsetSet('key6', 'value6');

        $this->assertEquals(6, count($this->fields));
    }

    public function testGet()
    {
        $this->assertEquals('value1', $this->fields['key1']);
        $this->assertEquals('value1', $this->fields->key1);
        $this->assertEquals('value1', $this->fields->get('key1'));
        $this->assertEquals('value1', $this->fields->offsetGet('key1'));
        $this->assertEquals(null, $this->fields->offsetGet('not exist'));
    }

    public function testExists()
    {
        $this->assertTrue(isset($this->fields['key1']));
        $this->assertTrue($this->fields->offsetExists('key1'));
    }

    public function testUnset()
    {
        unset($this->fields['key1']);
        $this->assertFalse(isset($this->fields['key1']));

        $this->fields->offsetUnset('key2');
        $this->assertFalse(isset($this->fields['key2']));
    }

    public function testIterator()
    {
        $iterator = $this->fields->getIterator();

        $this->assertEquals('value1', $iterator->current());

        $iterator->next();
        $this->assertEquals('value2', $iterator->current());
    }

    public function testCount()
    {
        $this->assertEquals(2, count($this->fields));
        $this->assertEquals(2, $this->fields->count());
    }
}
