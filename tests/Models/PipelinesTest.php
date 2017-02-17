<?php

class PipelinesMock extends \AmoCRM\Models\Pipelines
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['pipelines' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'pipelines' => [
                'add' => [
                    'pipelines' => [
                        ['id' => 100],
                        ['id' => 200]
                    ]
                ],
                'update' => [
                    'pipelines' => [
                        ['id' => 100],
                        ['id' => 200]
                    ]
                ]
            ]
        ];
    }
}

class PipelinesTest extends TestCase
{
    /**
     * @var null|PipelinesMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new PipelinesMock($paramsBag);
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
        $this->assertEquals('/private/api/v2/json/pipelines/list', $this->model->mockUrl);
        $this->assertEmpty($this->model->mockParameters);

        $result = $this->model->apiList(100);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/pipelines/list', $this->model->mockUrl);
        $this->assertEquals(['id' => 100], $this->model->mockParameters);
    }

    public function testApiAdd()
    {
        $expected = [
            'pipelines' => [
                'add' => [
                    [
                        'name' => 'Воронка 1',
                        'sort' => 1,
                        'is_main' => 'on',
                        'statuses' => [
                            0 => [
                                'name' => 'Pending',
                                'sort' => 10,
                                'color' => '#fffeb2',
                            ],
                            12345 => [
                                'id' => 12345,
                                'name' => 'Done',
                                'sort' => 20,
                                'color' => '#f3beff',
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $this->model['name'] = 'Воронка 1';
        $this->model['sort'] = 1;
        $this->model['is_main'] = 1;
        $this->model->addStatusField([
            'name' => 'Pending',
            'sort' => 10,
            'color' => '#fffeb2',
        ]);
        $this->model->addStatusField([
            'name' => 'Done',
            'sort' => 20,
            'color' => '#f3beff',
        ], 12345);

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/pipelines/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);

        $expected = [
            'pipelines' => [
                'add' => [
                    [
                        'name' => 'Воронка 1',
                        'sort' => 1,
                        'is_main' => 'on',
                        'statuses' => [
                            0 => [
                                'name' => 'Pending',
                                'sort' => 10,
                                'color' => '#fffeb2',
                            ],
                            12345 => [
                                'id' => 12345,
                                'name' => 'Done',
                                'sort' => 20,
                                'color' => '#f3beff',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Воронка 1',
                        'sort' => 1,
                        'is_main' => 'on',
                        'statuses' => [
                            0 => [
                                'name' => 'Pending',
                                'sort' => 10,
                                'color' => '#fffeb2',
                            ],
                            12345 => [
                                'id' => 12345,
                                'name' => 'Done',
                                'sort' => 20,
                                'color' => '#f3beff',
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/pipelines/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
    }

    public function testApiUpdate()
    {
        $this->model['name'] = 'Воронка';

        $this->assertTrue($this->model->apiUpdate(1));
        $this->assertEquals('/private/api/v2/json/pipelines/set', $this->model->mockUrl);
        $this->assertEquals(1, $this->model->mockParameters['pipelines']['update'][0]['id']);
        $this->assertEquals('Воронка', $this->model->mockParameters['pipelines']['update'][0]['name']);
    }

    public function testApiDelete()
    {
        $this->model->apiDelete(1);
        $this->assertEquals('/private/api/v2/json/pipelines/delete', $this->model->mockUrl);
        $this->assertEquals(['id' => 1], $this->model->mockParameters);

        $this->model->apiDelete([1, 2]);
        $this->assertEquals('/private/api/v2/json/pipelines/delete', $this->model->mockUrl);
        $this->assertEquals(['id' => [1, 2]], $this->model->mockParameters);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['name', 'ФИО', 'ФИО'],
            ['sort', 100, 100],
            ['is_main', 'on', 'on'],
            ['is_main', 1, 'on'],
            ['is_main', true, 'on'],
        ];
    }
}
