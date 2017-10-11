<?php

class CustomerMock extends \AmoCRM\Models\Customer
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['customers' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'customers' => [
                'add' => [
                    'customers' => [
                        ['id' => 100],
                        ['id' => 200]
                    ]
                ],
                'update' => [
                    'customers' => [
                        ['id' => 100],
                        ['id' => 200]
                    ]
                ]
            ]
        ];
    }
}

class CustomerTest extends TestCase
{
    /**
     * @var null|CustomerMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new CustomerMock($paramsBag);
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
        $parameters = [
            'limit_rows' => 100,
        ];

        $result = $this->model->apiList($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/customers/list', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $expected = [
            'customers' => [
                'add' => [
                    [
                        'name' => 'ФИО',
                        'request_id' => 100,
                        'main_user_id' => 200,
                        'next_price' => 300,
                        'periodicity' => 7,
                    ]
                ]
            ]
        ];

        $this->model['name'] = 'ФИО';
        $this->model['request_id'] = 100;
        $this->model['main_user_id'] = 200;
        $this->model['next_price'] = 300;
        $this->model['periodicity'] = 7;

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/customers/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'customers' => [
                'add' => [
                    [
                        'name' => 'ФИО',
                        'request_id' => 100,
                        'main_user_id' => 200,
                        'next_price' => 300,
                        'periodicity' => 7,
                    ],
                    [
                        'name' => 'ФИО',
                        'request_id' => 100,
                        'main_user_id' => 200,
                        'next_price' => 300,
                        'periodicity' => 7,
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/customers/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUpdate()
    {
        $this->model['name'] = 'ФИО';
        $this->model['main_user_id'] = 200;

        $this->assertTrue($this->model->apiUpdate(1));
        $this->assertEquals('/private/api/v2/json/customers/set', $this->model->mockUrl);

        $this->assertEquals(1, $this->model->mockParameters['customers']['update'][0]['id']);
        $this->assertEquals('ФИО', $this->model->mockParameters['customers']['update'][0]['name']);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['name', 'ФИО', 'ФИО'],
            ['main_user_id', 100, 100],
            ['created_by', 100, 100],
            ['next_price', 100, 100],
            ['periodicity', 100, 100],
            ['tags', 'Tag', 'Tag'],
            ['tags', ['Tag 1', 'Tag 2'], 'Tag 1,Tag 2'],
            ['next_date', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['request_id', 100, 100],
        ];
    }
}
