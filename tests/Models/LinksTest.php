<?php

class LinksMock extends \AmoCRM\Models\Links
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['links' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'links' => [
                'link' => [
                    'links' => [],
                    'errors' => [],
                ],
                'unlink' => [
                    'links' => [],
                    'errors' => [],
                ]
            ]
        ];
    }
}

class LinksTest extends TestCase
{
    /**
     * @var null|LinksMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new LinksMock($paramsBag);
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
            'from' => 'leads',
            'from_id' => 1125199,
            'to' => 'contacts',
            'to_id' => 3673249
        ];

        $result = $this->model->apiList($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/links/list', $this->model->mockUrl);
        $this->assertEquals(['links' => [$parameters]], $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiLink()
    {
        $expected = [
            'links' => [
                'link' => [
                    [
                        'from' => 'leads',
                        'from_id' => 1125199,
                        'to' => 'contacts',
                        'to_id' => 3673249,
                    ]
                ]
            ]
        ];

        $this->model['from'] = 'leads';
        $this->model['from_id'] = 1125199;
        $this->model['to'] = 'contacts';
        $this->model['to_id'] = 3673249;

        $this->assertTrue($this->model->apiLink());
        $this->assertEquals('/private/api/v2/json/links/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'links' => [
                'link' => [
                    [
                        'from' => 'leads',
                        'from_id' => 1125199,
                        'to' => 'contacts',
                        'to_id' => 3673249,
                    ],
                    [
                        'from' => 'leads',
                        'from_id' => 1125199,
                        'to' => 'contacts',
                        'to_id' => 3673249,
                    ]
                ]
            ]
        ];

        $this->assertTrue($this->model->apiLink([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/links/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUnlink()
    {
        $expected = [
            'links' => [
                'unlink' => [
                    [
                        'from' => 'leads',
                        'from_id' => 1125199,
                        'to' => 'contacts',
                        'to_id' => 3673249,
                    ]
                ]
            ]
        ];

        $this->model['from'] = 'leads';
        $this->model['from_id'] = 1125199;
        $this->model['to'] = 'contacts';
        $this->model['to_id'] = 3673249;

        $this->assertTrue($this->model->apiUnlink());
        $this->assertEquals('/private/api/v2/json/links/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'links' => [
                'unlink' => [
                    [
                        'from' => 'leads',
                        'from_id' => 1125199,
                        'to' => 'contacts',
                        'to_id' => 3673249,
                    ],
                    [
                        'from' => 'leads',
                        'from_id' => 1125199,
                        'to' => 'contacts',
                        'to_id' => 3673249,
                    ]
                ]
            ]
        ];

        $this->assertTrue($this->model->apiUnlink([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/links/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['from', 'leads', 'leads'],
            ['from_id', 100, 100],
            ['to', 'contacts', 'contacts'],
            ['to_id', 200, 200],
            ['from_catalog_id', 300, 300],
            ['to_catalog_id', 400, 400],
            ['quantity', 500, 500],
        ];
    }
}
