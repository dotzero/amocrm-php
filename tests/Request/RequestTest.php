<?php

namespace AmoCRM\Tests\Request;

use AmoCRM\Tests\TestCase;
use AmoCRM\Request\ParamsBag;
use AmoCRM\Request\Request;

class RequestTest extends TestCase
{
    /**
     * @var Request
     */
    private $request;

    public function setUp()
    {
        $paramsBag = new ParamsBag();
        $paramsBag->addAuth('domain', 'example.amocrm.ru');
        $paramsBag->addAuth('login', 'login@domain');
        $paramsBag->addAuth('apikey', 'hash');

        $paramsBag->addGet('param1', 'value1');
        $paramsBag->addGet('param2', 'value2');

        $paramsBag->addPost('field1', 'value1');
        $paramsBag->addPost('field2', 'value2');

        $this->request = new Request();
        $this->request->setParameters($paramsBag);
    }

    public function testGetParameters()
    {
        $actual = $this->request->getParameters();

        $this->assertInstanceOf('\AmoCRM\Request\ParamsBagInterface', $actual);
        $this->assertEquals('value1', $actual->getGet('param1'));
        $this->assertEquals('value2', $actual->getGet('param2'));
    }

    public function testSetLogger()
    {
        $this->assertInstanceOf('\Psr\Log\LoggerAwareInterface', $this->request);
        $this->assertAttributeInstanceOf('\Psr\Log\LoggerInterface', 'logger', $this->request);
        $this->assertAttributeInstanceOf('\Psr\Log\NullLogger', 'logger', $this->request);

        $mock = $this->getMockBuilder('\Psr\Log\NullLogger')->getMock();
        $this->request->setLogger($mock);

        $this->assertAttributeInstanceOf('\Psr\Log\LoggerInterface', 'logger', $this->request);
        $this->assertAttributeSame($mock, 'logger', $this->request);
    }

    public function testGetRequest()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Request\Request')
            ->setMethods(['request'])
            ->getMock();

        $mock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('/foobar'))
            ->willReturnArgument(1);

        $modified = $this->invokeMethod($mock, 'getRequest', [
            '/foobar',
            ['foo' => 'bar'],
            'now'
        ]);

        $this->assertEquals('now', $modified);

        $params = $mock->getParameters();
        $this->assertEquals(['foo' => 'bar'], $params->getGet());
    }

    public function testPostRequest()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Request\Request')
            ->setMethods(['request'])
            ->getMock();

        $mock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('/foobar'))
            ->willReturnArgument(1);


        $modified = $this->invokeMethod($mock, 'postRequest', [
            '/foobar',
            ['foo' => 'bar']
        ]);

        $this->assertNull($modified);

        $params = $mock->getParameters();
        $this->assertEquals(['foo' => 'bar'], $params->getPost());
    }

    public function testPrepareHeaders()
    {
        date_default_timezone_set('UTC');

        $dt = '2017-01-02 12:30:00';
        $ts = strtotime($dt);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders');

        $this->assertCount(1, $actual);
        $this->assertContains('Content-Type: application/json', $actual);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders', [$dt]);
        $this->assertCount(2, $actual);
        $this->assertContains('IF-MODIFIED-SINCE: Mon, 02 Jan 2017 12:30:00 +0000', $actual);

        $actual = $this->invokeMethod($this->request, 'prepareHeaders', [$ts]);
        $this->assertCount(2, $actual);
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
        $this->setProtectedProperty($this->request, 'v1', true);

        $expected = 'https://example.amocrm.ru/foo/?param1=value1&param2=value2&login=login%40domain&api_key=hash';
        $actual = $this->invokeMethod($this->request, 'prepareEndpoint', ['/foo/']);

        $this->assertEquals($expected, $actual);
    }

    public function testPrepareEndpointV2()
    {
        $expected = 'https://example.amocrm.ru/foo/?param1=value1&param2=value2&USER_LOGIN=login%40domain&USER_HASH=hash';
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

        $this->setProtectedProperty($this->request, 'v1', true);
        $this->invokeMethod($this->request, 'parseResponse', [$response, $info]);
    }
}
