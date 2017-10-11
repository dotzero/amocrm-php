<?php

class CatalogMock extends \AmoCRM\Models\Catalog
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['contacts' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'catalogs' => [
                'add' => [
                    'catalogs' => [
                        ['id' => 100],
                        ['id' => 100],
                    ],
                    'errors' => [],
                ],
                'update' => [
                    'catalogs' => [
                        ['id' => 100]
                    ],
                    'errors' => [],
                ],
                'delete' => [
                    'catalogs' => [
                        ['id' => 100]
                    ],
                    'errors' => [],
                ]
            ]
        ];
    }
}

class CatalogTest extends TestCase
{
    /**
     * @var null|CatalogMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new CatalogMock($paramsBag);
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
        $this->assertEquals('/private/api/v2/json/catalogs/list', $this->model->mockUrl);
        $this->assertEquals([], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $result = $this->model->apiList(100);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/catalogs/list', $this->model->mockUrl);
        $this->assertEquals(['id' => 100], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $expected = [
            'catalogs' => [
                'add' => [
                    [
                        'name' => 'Products',
                    ]
                ]
            ]
        ];

        $this->model['name'] = 'Products';

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/catalogs/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'catalogs' => [
                'add' => [
                    [
                        'name' => 'Products',
                    ],
                    [
                        'name' => 'Products',
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/catalogs/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUpdate()
    {
        $this->model['name'] = 'Products';
        $this->model['company_name'] = 'ООО Тестовая компания';

        $this->assertTrue($this->model->apiUpdate(1));
        $this->assertEquals('/private/api/v2/json/catalogs/set', $this->model->mockUrl);
        $this->assertEquals(1, $this->model->mockParameters['catalogs']['update'][0]['id']);
        $this->assertEquals('Products', $this->model->mockParameters['catalogs']['update'][0]['name']);
    }

    public function testApiDelete()
    {
        $this->assertTrue($this->model->apiDelete(1));
        $this->assertEquals('/private/api/v2/json/catalogs/set', $this->model->mockUrl);
        $this->assertEquals([1], $this->model->mockParameters['catalogs']['delete']);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['name', 'Products', 'Products'],
            ['request_id', 100, 100],
        ];
    }
}
