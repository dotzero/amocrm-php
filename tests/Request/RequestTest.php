<?php

class RequestMock extends \AmoCRM\Request\Request
{
    protected function request($url, $modified = null)
    {
        return [];
    }
}

class RequestTest extends PHPUnit_Framework_TestCase
{
    private $request = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->request = new RequestMock($paramsBag);
    }

    public function testDebug()
    {
        $this->assertAttributeEquals(false, 'debug', $this->request);
        $this->request->debug(true);
        $this->assertAttributeEquals(true, 'debug', $this->request);
    }

    public function testGetRequest()
    {
        $actual = $this->invokeMethod($this->request, 'getRequest', [
            '/foobar',
            ['foo' => 'bar'],
            'now'
        ]);

        $this->assertEquals([], $actual);
    }

    public function testPostRequest()
    {
        $actual = $this->invokeMethod($this->request, 'postRequest', [
            '/foobar',
            ['foo' => 'bar']
        ]);

        $this->assertEquals([], $actual);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
