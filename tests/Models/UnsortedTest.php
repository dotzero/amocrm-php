<?php

class UnsortedMock extends \AmoCRM\Models\Unsorted
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['unsorted' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'unsorted' => [
                'add' => [
                    'data' => [
                        '100' => 100,
                    ]
                ],
            ]
        ];
    }
}

class UnsortedTest extends TestCase
{
    /**
     * @var null|UnsortedMock
     */
    private $model = null;

    /**
     * @var null|\AmoCRM\Request\ParamsBag
     */
    private $paramsBag = null;

    public function setUp()
    {
        $this->paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new UnsortedMock($this->paramsBag);
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
            'page_size' => 10,
            'PAGEN_1' => 1,
        ];

        $result = $this->model->apiList($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/api/unsorted/list/', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiGetAllSummary()
    {
        $result = $this->model->apiGetAllSummary();

        $this->assertEquals([], $result);
        $this->assertEquals('/api/unsorted/get_all_summary/', $this->model->mockUrl);
        $this->assertEmpty($this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $expected = [
            'unsorted' => [
                'category' => 'mail',
                'add' => [
                    [
                        'source' => 'some@mail.from',
                        'source_uid' => '06ea27be-b26e-4ce4-8c20-cb4261a65752',
                        'source_data' => [
                            'from' => [
                                'email' => 'info@site.hh.ru',
                                'name' => 'HeadHunter',
                            ],
                            'date' => 1446544372,
                            'subject' => 'Did you like me?',
                            'thread_id' => 11774,
                            'message_id' => 23698,
                        ],
                    ]
                ]
            ]
        ];

        $this->model['source'] = 'some@mail.from';
        $this->model['source_uid'] = '06ea27be-b26e-4ce4-8c20-cb4261a65752';
        $this->model['source_data'] = [
            'from' => [
                'email' => 'info@site.hh.ru',
                'name' => 'HeadHunter',
            ],
            'date' => 1446544372,
            'subject' => 'Did you like me?',
            'thread_id' => 11774,
            'message_id' => 23698,
        ];

        $this->assertEquals(100, $this->model->apiAdd(UnsortedMock::TYPE_MAIL));
        $this->assertEquals('/api/unsorted/add/', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAddSip()
    {
        $this->assertEquals(100, $this->model->apiAddSip());
        $this->assertEquals('/api/unsorted/add/', $this->model->mockUrl);
        $this->assertEquals('sip', $this->model->mockParameters['unsorted']['category']);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAddMail()
    {
        $this->assertEquals(100, $this->model->apiAddMail());
        $this->assertEquals('/api/unsorted/add/', $this->model->mockUrl);
        $this->assertEquals('mail', $this->model->mockParameters['unsorted']['category']);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAddForms()
    {
        $this->assertEquals(100, $this->model->apiAddForms());
        $this->assertEquals('/api/unsorted/add/', $this->model->mockUrl);
        $this->assertEquals('forms', $this->model->mockParameters['unsorted']['category']);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAccept()
    {
        $expected = [
            'unsorted' => [
                'accept' => [
                    100
                ],
                'user_id' => 200,
                'status_id' => 300,
            ],
        ];

        $this->model->apiAccept(100, 200, 300);
        $this->assertEquals('/api/unsorted/accept/', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiDecline()
    {
        $expected = [
            'unsorted' => [
                'decline' => [
                    100
                ],
                'user_id' => 200,
            ],
        ];

        $this->model->apiDecline(100, 200);
        $this->assertEquals('/api/unsorted/decline/', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testAddDataLead()
    {
        $lead = new \AmoCRM\Models\Lead($this->paramsBag);
        $lead['name'] = 'New lead from this form';

        $this->model->addDataLead($lead);
        $this->assertArrayHasKey('leads', $this->model['data']);
        $this->assertCount(1, $this->model['data']['leads']);
        $this->assertEquals($lead->getValues(), $this->model['data']['leads'][0]);

        $this->model->addDataLead([$lead, $lead]);
        $this->assertArrayHasKey('leads', $this->model['data']);
        $this->assertCount(3, $this->model['data']['leads']);
        $this->assertEquals($lead->getValues(), $this->model['data']['leads'][1]);
        $this->assertEquals($lead->getValues(), $this->model['data']['leads'][2]);
    }

    public function testAddDataContact()
    {
        $contact = new \AmoCRM\Models\Contact($this->paramsBag);
        $contact['name'] = 'New contact from this form';

        $this->model->addDataContact($contact);
        $this->assertArrayHasKey('contacts', $this->model['data']);
        $this->assertCount(1, $this->model['data']['contacts']);
        $this->assertEquals($contact->getValues(), $this->model['data']['contacts'][0]);

        $this->model->addDataContact([$contact, $contact]);
        $this->assertArrayHasKey('contacts', $this->model['data']);
        $this->assertCount(3, $this->model['data']['contacts']);
        $this->assertEquals($contact->getValues(), $this->model['data']['contacts'][1]);
        $this->assertEquals($contact->getValues(), $this->model['data']['contacts'][2]);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['source', 'www.my-awesome-site.com', 'www.my-awesome-site.com'],
            ['source_uid', null, null],
            ['source_uid', '06ea27be-b26e-4ce4-8c20-cb4261a65752', '06ea27be-b26e-4ce4-8c20-cb4261a65752'],
            ['date_create', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['pipeline_id', 100, 100],
        ];
    }
}
