<?php

class WebhooksMock extends \AmoCRM\Models\Webhooks
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

class WebhooksTest extends TestCase
{
    /**
     * @var null|WebhooksMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new WebhooksMock($paramsBag);
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testFields($field, $value, $expected)
    {
        $this->model[$field] = $value;

        $this->assertEquals($this->model[$field], $expected);
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

    public function testApiSubscribe()
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

    public function testApiSubscribeModel()
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

        $this->model['url'] = 'http://example.com/';
        $this->model['events'] = 'status_lead';

        $result = $this->model->apiSubscribe();

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

    public function testApiUnsubscribe()
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

    public function testApiUnsubscribeModel()
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

        $this->model['url'] = 'http://example.com/';
        $this->model['events'] = 'status_lead';

        $result = $this->model->apiUnsubscribe();

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

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['url', 'http://example.com/', 'http://example.com/'],
            ['events', 'status_lead', ['status_lead']],
            [
                'events',
                [
                    'add_contact',
                    'update_contact',
                    'delete_contact'
                ],
                [
                    'add_contact',
                    'update_contact',
                    'delete_contact'
                ]
            ],
            [
                'events',
                null,
                [
                    'add_lead', // Добавить сделку
                    'add_contact', // Добавить контакт
                    'add_company', // Добавить компанию
                    'add_customer', // Добавить покупателя
                    'update_lead', // Изменить сделку
                    'update_contact', // Изменить контакт
                    'update_company', // Изменить компанию
                    'update_customer', // Изменить покупателя
                    'delete_lead', // Удалить сделку
                    'delete_contact', // Удалить контакт
                    'delete_company', // Удалить компанию
                    'delete_customer', // Удалить покупателя
                    'status_lead', // Смена статуса сделки
                    'responsible_lead', // Смена отв-го сделки
                    'restore_contact', // Восстановить контакт
                    'restore_company', // Восстановить компанию
                    'restore_lead', // Восстановить сделку
                    'note_lead', // Примечание в сделке
                    'note_contact', // Примечание в контакте
                    'note_company', // Примечание в компании
                    'note_customer', // Примечание в покупателе
                ]
            ],
        ];
    }
}
