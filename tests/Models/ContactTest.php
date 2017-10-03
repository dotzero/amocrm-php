<?php

class ContactMock extends \AmoCRM\Models\Contact
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
            'contacts' => [
                'add' => [
                    ['id' => 100],
                    ['id' => 200]
                ],
                'update' => [
                    ['id' => 100],
                    ['id' => 200]
                ]
            ]
        ];
    }
}

class ContactTest extends TestCase
{
    /**
     * @var null|ContactMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new ContactMock($paramsBag);
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testFields($field, $value, $expected)
    {
        $this->model[$field] = $value;

        $this->assertEquals($this->model[$field], $expected);
    }

    public function testCustomFields()
    {
        $this->model->addCustomField(100, 'Custom text');
        $this->model->addCustomField(200, 'test@mail.com', 'WORK');
        $this->model->addCustomField(300, [
            ['415.874.3275', 'MOB'],
            ['415.374.3278', 'OTHER'],
            ['415.374.3279', 'FAX'],
        ]);
        $this->model->addCustomMultiField(400, 100);
        $this->model->addCustomMultiField(500, [200, 300]);

        $this->assertArrayHasKey('id', $this->model['custom_fields'][0]);
        $this->assertArrayHasKey('values', $this->model['custom_fields'][0]);
        $this->assertArrayHasKey('value', $this->model['custom_fields'][0]['values'][0]);
        $this->assertEquals('Custom text', $this->model['custom_fields'][0]['values'][0]['value']);

        $this->assertArrayHasKey('id', $this->model['custom_fields'][1]);
        $this->assertArrayHasKey('values', $this->model['custom_fields'][1]);
        $this->assertArrayHasKey('value', $this->model['custom_fields'][1]['values'][0]);
        $this->assertEquals('test@mail.com', $this->model['custom_fields'][1]['values'][0]['value']);
        $this->assertEquals('WORK', $this->model['custom_fields'][1]['values'][0]['enum']);

        $this->assertArrayHasKey('id', $this->model['custom_fields'][2]);
        $this->assertArrayHasKey('values', $this->model['custom_fields'][2]);
        $this->assertCount(3, $this->model['custom_fields'][2]['values']);

        $this->assertArrayHasKey('id', $this->model['custom_fields'][3]);
        $this->assertArrayHasKey('values', $this->model['custom_fields'][3]);
        $this->assertCount(1, $this->model['custom_fields'][3]['values']);
        $this->assertContains(100, $this->model['custom_fields'][3]['values']);

        $this->assertArrayHasKey('id', $this->model['custom_fields'][4]);
        $this->assertArrayHasKey('values', $this->model['custom_fields'][4]);
        $this->assertCount(2, $this->model['custom_fields'][4]['values']);
        $this->assertContains(200, $this->model['custom_fields'][4]['values']);
        $this->assertContains(300, $this->model['custom_fields'][4]['values']);
    }

    public function testSetNotes()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $note = new \AmoCRM\Models\Note($paramsBag);
        $note['text'] = 'foobar';

        $this->model['notes'] = $note;
        $this->assertEquals($this->model['notes'], [$note->getValues()]);

        $this->model['notes'] = [$note, $note];
        $this->assertEquals($this->model['notes'], [$note->getValues(), $note->getValues()]);
    }

    public function testApiList()
    {
        $parameters = [
            'query' => 'test',
        ];

        $result = $this->model->apiList($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/contacts/list', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $expected = [
            'contacts' => [
                'add' => [
                    [
                        'name' => 'ФИО',
                        'company_name' => 'ООО Тестовая компания',
                    ]
                ]
            ]
        ];

        $this->model['name'] = 'ФИО';
        $this->model['company_name'] = 'ООО Тестовая компания';

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/contacts/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'contacts' => [
                'add' => [
                    [
                        'name' => 'ФИО',
                        'company_name' => 'ООО Тестовая компания',
                    ],
                    [
                        'name' => 'ФИО',
                        'company_name' => 'ООО Тестовая компания',
                    ]
                ]
            ]
        ];

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/contacts/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUpdate()
    {
        $this->model['name'] = 'ФИО';
        $this->model['company_name'] = 'ООО Тестовая компания';

        $this->assertTrue($this->model->apiUpdate(1));
        $this->assertEquals('/private/api/v2/json/contacts/set', $this->model->mockUrl);
        $this->assertEquals(1, $this->model->mockParameters['contacts']['update'][0]['id']);
        $this->assertEquals('ФИО', $this->model->mockParameters['contacts']['update'][0]['name']);

        $this->assertTrue($this->model->apiUpdate(1, 'now'));
        $this->assertEquals('/private/api/v2/json/contacts/set', $this->model->mockUrl);
        $this->assertEquals(1, $this->model->mockParameters['contacts']['update'][0]['id']);
        $this->assertEquals('ФИО', $this->model->mockParameters['contacts']['update'][0]['name']);
    }

    public function testApiLinks()
    {
        $parameters = [
            'limit_rows' => 4
        ];

        $result = $this->model->apiLinks($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/contacts/links', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['name', 'ФИО', 'ФИО'],
            ['request_id', 100, 100],
            ['date_create', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['last_modified', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['responsible_user_id', 100, 100],
            ['created_user_id', 100, 100],
            ['linked_leads_id', 100, [100]],
            ['linked_leads_id', [100, 200], [100, 200]],
            ['company_name', 'Компания', 'Компания'],
            ['linked_company_id', 100, 100],
            ['tags', 'Tag', 'Tag'],
            ['tags', ['Tag 1', 'Tag 2'], 'Tag 1,Tag 2'],
            ['modified_user_id', 100, 100],
        ];
    }
}
