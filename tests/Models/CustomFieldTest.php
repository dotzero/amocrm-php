<?php

class CustomFieldMock extends \AmoCRM\Models\CustomField
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'fields' => [
                'add' => [
                    ['id' => 100],
                    ['id' => 200]
                ],
                'delete' => [
                    ['id' => 100]
                ]
            ]
        ];
    }
}

class CustomFieldTest extends TestCase
{
    /**
     * @var null|CustomFieldMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new CustomFieldMock($paramsBag);
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testFields($field, $value, $expected)
    {
        $this->model[$field] = $value;

        $this->assertSame($this->model[$field], $expected);
    }

    public function testApiAdd()
    {
        $expected = [
            'fields' => [
                'add' => [
                    [
                        'name' => 'Tracking ID',
                        'type' => 1,
                        'element_type' => 3,
                        'origin' => '528d0285c1f9180911159a9dc6f759b3_zendesk_widget',
                    ]
                ]
            ]
        ];

        $this->model['name'] = 'Tracking ID';
        $this->model['type'] = \AmoCRM\Models\CustomField::TYPE_TEXT;
        $this->model['element_type'] = \AmoCRM\Models\CustomField::ENTITY_COMPANY;
        $this->model['origin'] = '528d0285c1f9180911159a9dc6f759b3_zendesk_widget';

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/fields/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'fields' => [
                'add' => [
                    [
                        'name' => 'Tracking ID',
                        'type' => 1,
                        'element_type' => 3,
                        'origin' => '528d0285c1f9180911159a9dc6f759b3_zendesk_widget',
                    ],
                    [
                        'name' => 'Tracking ID',
                        'type' => 1,
                        'element_type' => 3,
                        'origin' => '528d0285c1f9180911159a9dc6f759b3_zendesk_widget',
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/fields/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiDelete()
    {
        $expected = [
            'fields' => [
                'delete' => [
                    [
                        'id' => 100,
                        'origin' => '528d0285c1f9180911159a9dc6f759b3_zendesk_widget',
                    ],
                ]
            ]
        ];

        $this->assertTrue($this->model->apiDelete(100, '528d0285c1f9180911159a9dc6f759b3_zendesk_widget'));
        $this->assertEquals('/private/api/v2/json/fields/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['name', 'Field', 'Field'],
            ['request_id', 100, 100],
            ['disabled', 1, 1],
            ['disabled', true, 1],
            ['disabled', 0, 0],
            ['disabled', false, 0],
            ['type', 1, 1],
            ['element_type', 1, 1],
            ['origin', 'api', 'api'],
            ['enums', [['one'], ['two']], [['one'], ['two']]],
        ];
    }
}
