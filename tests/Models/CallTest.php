<?php

class CallMock extends \AmoCRM\Models\Call
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['calls' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'calls' => [
                'add' => [
                    'success' => [
                        "b7095fb33b368c7103626d3943d9e61c14697",
                        "b8b49f89f4c9b6fde609ad741e640dd946234",
                    ],
                    'errors' => [],
                ]
            ]
        ];
    }
}

class CallTest extends TestCase
{
    /**
     * @var null|CallMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new CallMock($paramsBag);
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testFields($field, $value, $expected)
    {
        $this->model[$field] = $value;

        $this->assertEquals($this->model[$field], $expected);
    }

    public function testApiAdd()
    {
        $expected = [
            'add' => [
                [
                    'account_id' => 1111111,
                    'uuid' => '947669bc-ec58-450e-83e8-828a3e6fc354',
                ]
            ]
        ];

        $this->model['account_id'] = 1111111;
        $this->model['uuid'] = '947669bc-ec58-450e-83e8-828a3e6fc354';

        $this->assertEquals(
            'b7095fb33b368c7103626d3943d9e61c14697',
            $this->model->apiAdd('code', 'key')
        );
        $this->assertEquals('/api/calls/add/', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'add' => [
                [
                    'account_id' => 1111111,
                    'uuid' => '947669bc-ec58-450e-83e8-828a3e6fc354',
                ],
                [
                    'account_id' => 1111111,
                    'uuid' => '947669bc-ec58-450e-83e8-828a3e6fc354',
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd('code', 'key', [$this->model, $this->model]));
        $this->assertEquals('/api/calls/add/', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['account_id', 100, 100],
            ['uuid', '947669bc-ec58-450e-83e8-828a3e6fc354', '947669bc-ec58-450e-83e8-828a3e6fc354'],
            ['caller', '88001000000', '88001000000'],
            ['to', '88002000000', '88002000000'],
            ['date', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['type', 'inbound', 'inbound'],
            ['billsec', 10, 10],
            ['link', 'http://example.com/audio.mp3', 'http://example.com/audio.mp3'],
        ];
    }
}
