<?php

class CatalogElementMock extends \AmoCRM\Models\CatalogElement
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['catalog_elements' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'catalog_elements' => [
                'add' => [
                    'catalog_elements' => [
                        ['id' => 100],
                        ['id' => 100],
                    ],
                    'errors' => [],
                ],
                'update' => [
                    'catalog_elements' => [
                        ['id' => 100]
                    ],
                    'errors' => [],
                ],
                'delete' => [
                    'catalog_elements' => [
                        ['id' => 100]
                    ],
                    'errors' => [],
                ]
            ]
        ];
    }
}

class CatalogElementTest extends TestCase
{
    /**
     * @var null|CatalogElementMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new CatalogElementMock($paramsBag);
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
        $this->assertEquals('/private/api/v2/json/catalog_elements/list', $this->model->mockUrl);
        $this->assertEquals([], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $expected = [
            'catalog_elements' => [
                'add' => [
                    [
                        'catalog_id' => 100,
                        'name' => 'Product',
                    ]
                ]
            ]
        ];

        $this->model['catalog_id'] = 100;
        $this->model['name'] = 'Product';

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/catalog_elements/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'catalog_elements' => [
                'add' => [
                    [
                        'catalog_id' => 100,
                        'name' => 'Product',
                    ],
                    [
                        'catalog_id' => 100,
                        'name' => 'Product',
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/catalog_elements/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUpdate()
    {
        $this->model['name'] = 'Product';

        $this->assertTrue($this->model->apiUpdate(1));
        $this->assertEquals('/private/api/v2/json/catalog_elements/set', $this->model->mockUrl);
        $this->assertEquals(1, $this->model->mockParameters['catalog_elements']['update'][0]['id']);
        $this->assertEquals('Product', $this->model->mockParameters['catalog_elements']['update'][0]['name']);
    }

    public function testApiDelete()
    {
        $this->assertTrue($this->model->apiDelete(1));
        $this->assertEquals('/private/api/v2/json/catalog_elements/set', $this->model->mockUrl);
        $this->assertEquals([1], $this->model->mockParameters['catalog_elements']['delete']);
    }

    public function testApiDeleteBatch()
    {
        $this->assertTrue($this->model->apiDeleteBatch([1,2,3]));
        $this->assertEquals('/private/api/v2/json/catalog_elements/set', $this->model->mockUrl);
        $this->assertEquals([1,2,3], $this->model->mockParameters['catalog_elements']['delete']);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['catalog_id', 100, 100],
            ['name', 'Products', 'Products'],
            ['request_id', 200, 200],
        ];
    }
}
