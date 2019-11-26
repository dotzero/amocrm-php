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

class RequestTest extends TestCase
{
    /**
     * @var null|RequestMock
     */
    private $request = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $paramsBag->addAuth('domain', 'example.amocrm.ru');
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

    public function testGetLastHttpCode()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $request = new \AmoCRM\Request\Request($paramsBag);

        $this->setProtectedProperty($request, 'lastHttpCode', 200);
        $this->assertEquals(200, $request->getLastHttpCode());
    }

    public function testGetLastHttpResponse()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $request = new \AmoCRM\Request\Request($paramsBag);

        $this->setProtectedProperty($request, 'lastHttpResponse', 'foobar');
        $this->assertEquals('foobar', $request->getLastHttpResponse());
    }

    public function testGetParameters()
    {
        $actual = $this->invokeMethod($this->request, 'getParameters');
        $this->assertInstanceOf('\AmoCRM\Request\ParamsBag', $actual);
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
        date_default_timezone_set('UTC');

        $dt = '2017-01-02 12:30:00';
        $ts = strtotime($dt);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders');

        $this->assertCount(2, $actual);
        $this->assertContains('Content-Type: application/json', $actual);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders', [$dt]);
        $this->assertCount(3, $actual);
        $this->assertContains('IF-MODIFIED-SINCE: Mon, 02 Jan 2017 12:30:00 +0000', $actual);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders', [$ts]);
        $this->assertCount(3, $actual);
        $this->assertContains('IF-MODIFIED-SINCE: 1483360200', $actual);
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

    public function testParseResponse()
    {
        $response = json_encode([
            'response' => [
                'foo' => 'bar',
            ]
        ]);
        $info = [
            'http_code' => 200
        ];

        $actual = $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
        $this->assertArrayHasKey('foo', $actual);
        $this->assertEquals('bar', $actual['foo']);
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
     * @expectedException \AmoCRM\Exception
     * @expectedExceptionCode 403
     * @expectedExceptionMessage Аккаунт заблокирован, за неоднократное превышение количества запросов в секунду
     */
    public function testParseInvalidResponseWithHttpError()
    {
        $response = '~~GO AWAY~~';
        $info = [
            'http_code' => 403,
        ];

        $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
    }

    /**
     * @expectedException \AmoCRM\Exception
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Invalid response body.
     */
    public function testParseInvalidResponseWithHttpError502()
    {
        $response = '<html>Bad Gateway</html>';
        $info = [
            'http_code' => 502,
        ];

        $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
    }

    public function testPrintDebug()
    {
        $this->request->debug(true);

        $actual = $this->invokeMethod($this->request, 'printDebug', ['foo', 'bar', true]);
        $this->assertStringStartsWith('[DEBUG]', $actual);
        $this->assertRegExp('/foo: bar/u', $actual);

        $actual = $this->invokeMethod($this->request, 'printDebug', ['foo', [100 => 200], true]);
        $this->assertStringStartsWith('[DEBUG]', $actual);
        $this->assertRegExp('/Array/u', $actual);
        $this->assertRegExp('/\[100\] => 200/u', $actual);
    }

    public function testPrintDebugOff()
    {
        $actual = $this->invokeMethod($this->request, 'printDebug');
        $this->assertFalse($actual);
    }
}
