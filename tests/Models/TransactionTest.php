<?php

class TransactionMock extends \AmoCRM\Models\Transaction
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['transactions' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'transactions' => [
                'add' => [
                    'transactions' => [
                        ['id' => 100],
                        ['id' => 100],
                    ],
                    'errors' => [],
                ],
                'delete' => [
                    'transactions' => [
                        ['id' => 100]
                    ],
                    'errors' => [],
                ]
            ]
        ];
    }
}

class TransactionTest extends TestCase
{
    /**
     * @var null|TransactionMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new TransactionMock($paramsBag);
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

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/transactions/list', $this->model->mockUrl);
        $this->assertEquals([], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $expected = [
            'transactions' => [
                'add' => [
                    [
                        'customer_id' => 29729,
                        'price' => 3500,
                    ]
                ]
            ]
        ];

        $this->model['customer_id'] = 29729;
        $this->model['price'] = 3500;

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/transactions/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'transactions' => [
                'add' => [
                    [
                        'customer_id' => 29729,
                        'price' => 3500,
                    ],
                    [
                        'customer_id' => 29729,
                        'price' => 3500,
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/transactions/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiDelete()
    {
        $this->assertTrue($this->model->apiDelete(1));
        $this->assertEquals('/private/api/v2/json/transactions/set', $this->model->mockUrl);
        $this->assertEquals([1], $this->model->mockParameters['transactions']['delete']);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['customer_id', 100, 100],
            ['date', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['price', 200, 200],
            ['comment', 'Comment for transaction', 'Comment for transaction'],
            ['request_id', 300, 300],
            ['next_price', 400, 400],
            ['next_date', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
        ];
    }
}
