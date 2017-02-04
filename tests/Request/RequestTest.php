<?php

class RequestMock extends \AmoCRM\Request\Request
{
    public function v1($value)
    {
        $this->v1 = $value;
    }

    protected function request($url, $modified = null)
    {
        return [];
    }
}

class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|RequestMock
     */
    private $request = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $paramsBag->addAuth('domain', 'example');
        $paramsBag->addAuth('login', 'login@domain');
        $paramsBag->addAuth('apikey', 'hash');
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

    public function testPrepareHeaders()
    {
        $actual = $this->invokeMethod($this->request, 'prepareHeaders');

        $this->assertCount(1, $actual);
        $this->assertContains('Content-Type: application/json', $actual);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders', [
            '2017-01-02 12:30:00'
        ]);

        $this->assertCount(2, $actual);
        $this->assertRegExp('/^IF-MODIFIED-SINCE: Mon, 02 Jan 2017 12:30:00/ui', $actual[1], $actual[1]);
    }

    /**
     * @expectedException \Exception
     */
    public function testIncorrectPrepareHeaders()
    {
        $this->invokeMethod($this->request, 'prepareHeaders', ['foobar']);
    }

    public function testPrepareEndpointV1()
    {
        $this->request->v1(true);
        $expected = 'https://example.amocrm.ru/foo/?login=login%40domain&api_key=hash';
        $actual = $this->invokeMethod($this->request, 'prepareEndpoint', ['/foo/']);

        $this->assertEquals($expected, $actual);
    }

    public function testPrepareEndpointV2()
    {
        $expected = 'https://example.amocrm.ru/foo/?USER_LOGIN=login%40domain&USER_HASH=hash';
        $actual = $this->invokeMethod($this->request, 'prepareEndpoint', ['/foo/']);

        $this->assertEquals($expected, $actual);
    }

    public function testParseResponseEmpty()
    {
        $actual = $this->invokeMethod($this->request, 'parseResponse', [null, null]);
        $this->assertFalse($actual);
    }

    /**
     * @expectedException \AmoCRM\Exception
     * @expectedExceptionCode 101
     * @expectedExceptionMessage Аккаунт не найден
     */
    public function testParseResponseWithError()
    {
        $response = json_encode([
            'response' => [
                'error_code' => '101',
                'error' => 'Аккаунт не найден',
            ]
        ]);
        $info = [
            'http_code' => 400
        ];

        $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
    }

    /**
     * @expectedException \AmoCRM\Exception
     * @expectedExceptionCode 0
     * @expectedExceptionMessage Аккаунт не найден
     */
    public function testParseResponseWithoutCode()
    {
        $response = json_encode([
            'response' => [
                'error' => 'Аккаунт не найден',
            ]
        ]);
        $info = [
            'http_code' => 400
        ];

        $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
    }

    /**
     * @expectedException \AmoCRM\Exception
     * @expectedExceptionCode 0
     * @expectedExceptionMessage {"foo":"bar"}
     */
    public function testParseResponseWithErrorV1()
    {
        $response = json_encode([
            'response' => [
                'foo' => 'bar',
            ]
        ]);
        $info = [
            'http_code' => 400
        ];

        $this->request->v1(true);
        $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
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
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
