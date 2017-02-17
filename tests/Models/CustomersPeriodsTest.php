<?php

class CustomersPeriodsMock extends \AmoCRM\Models\CustomersPeriods
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['customers_periods' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'customers_periods' => [
                'set' => [
                    ['id' => 100],
                    ['id' => 200],
                ]
            ]
        ];
    }
}

class CustomersPeriodsTest extends TestCase
{
    /**
     * @var null|CustomersPeriodsMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new CustomersPeriodsMock($paramsBag);
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
        $this->assertEquals('/private/api/v2/json/customers_periods/list', $this->model->mockUrl);
        $this->assertEquals([], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiSet()
    {
        $expected = [
            'customers_periods' => [
                'update' => [
                    [
                        'period' => 7,
                        'sort' => 1,
                        'color' => '#ffdc7f',
                    ]
                ]
            ]
        ];

        $this->model['period'] = 7;
        $this->model['sort'] = 1;
        $this->model['color'] = '#ffdc7f';

        $this->assertEquals(100, $this->model->apiSet());
        $this->assertEquals('/private/api/v2/json/customers_periods/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'customers_periods' => [
                'update' => [
                    [
                        'period' => 7,
                        'sort' => 1,
                        'color' => '#ffdc7f',
                    ],
                    [
                        'period' => 60,
                        'sort' => 3,
                        'color' => '#ccc8f9',
                    ]
                ]
            ]
        ];

        $period = [
            'period' => 60,
            'sort' => 3,
            'color' => '#ccc8f9',
        ];

        $this->assertCount(2, $this->model->apiSet([$this->model, $period]));
        $this->assertEquals('/private/api/v2/json/customers_periods/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['id', 100, 100],
            ['period', 60, 60],
            ['sort', 1, 1],
            ['color', '#ffdc7f', '#ffdc7f'],
        ];
    }
}
