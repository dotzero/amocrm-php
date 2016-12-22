<?php

class WebHooksMock extends \AmoCRM\Models\WebHooks
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return [
            'webhooks' => [
                [
                    'url' => 'http://example.com/',
                    'result' => 1,
                ]
            ]
        ];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'webhooks' => [
                'subscribe' => [
                    [
                        'url' => 'http://example.com/',
                        'result' => 1,
                    ]
                ],
                'unsubscribe' => [
                    [
                        'url' => 'http://example.com/',
                        'result' => 1,
                    ]
                ],
            ]
        ];
    }
}

class WebHooksTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|WebHooksMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new WebHooksMock($paramsBag);
    }

    public function testApiList()
    {
        $result = $this->model->apiList();

        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['result']);
        $this->assertEquals('/private/api/v2/json/webhooks/list', $this->model->mockUrl);
        $this->assertEquals([], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiSubscribeOne()
    {
        $expected = [
            'webhooks' => [
                'subscribe' => [
                    [
                        'url' => 'http://example.com/',
                        'events' => ['status_lead'],
                    ]
                ]
            ]
        ];

        $result = $this->model->apiSubscribe('http://example.com/', 'status_lead');

        $this->assertEquals(1, $result);
        $this->assertEquals('/private/api/v2/json/webhooks/subscribe', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiSubscribeMulti()
    {
        $expected = [
            'webhooks' => [
                'subscribe' => [
                    [
                        'url' => 'http://example.com/',
                        'events' => [
                            'add_contact',
                            'update_contact',
                            'delete_contact'
                        ],
                    ]
                ]
            ]
        ];

        $result = $this->model->apiSubscribe('http://example.com/', [
            'add_contact',
            'update_contact',
            'delete_contact'
        ]);

        $this->assertEquals(1, $result);
        $this->assertEquals('/private/api/v2/json/webhooks/subscribe', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUnsubscribeOne()
    {
        $expected = [
            'webhooks' => [
                'unsubscribe' => [
                    [
                        'url' => 'http://example.com/',
                        'events' => ['status_lead'],
                    ]
                ]
            ]
        ];

        $result = $this->model->apiUnsubscribe('http://example.com/', 'status_lead');

        $this->assertEquals(1, $result);
        $this->assertEquals('/private/api/v2/json/webhooks/unsubscribe', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUnsubscribeMulti()
    {
        $expected = [
            'webhooks' => [
                'unsubscribe' => [
                    [
                        'url' => 'http://example.com/',
                        'events' => [
                            'add_contact',
                            'update_contact',
                            'delete_contact'
                        ],
                    ]
                ]
            ]
        ];

        $result = $this->model->apiUnsubscribe('http://example.com/', [
            'add_contact',
            'update_contact',
            'delete_contact'
        ]);

        $this->assertEquals(1, $result);
        $this->assertEquals('/private/api/v2/json/webhooks/unsubscribe', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }
}
