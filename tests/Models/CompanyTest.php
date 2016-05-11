<?php

class CompanyTest extends PHPUnit_Framework_TestCase
{
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new \AmoCRM\Models\Company($paramsBag);
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
    }

    public function testApiList()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Models\Company')
            ->setConstructorArgs([new \AmoCRM\Request\ParamsBag()])
            ->setMethods(['apiList'])
            ->getMock();

        $this->assertInstanceOf('\AmoCRM\Models\Company', $mock);

        $mock->expects($this->once())->method('apiList')
            ->with($this->isType('array'))
            ->will($this->returnValue([]));

        $result = $mock->apiList([
            'query' => 'test',
        ]);

        $this->assertEquals([], $result);
    }

    public function testApiAdd()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Models\Company')
            ->setConstructorArgs([new \AmoCRM\Request\ParamsBag()])
            ->setMethods(['apiAdd'])
            ->getMock();

        $this->assertInstanceOf('\AmoCRM\Models\Company', $mock);

        $mock['name'] = 'ООО Тестовая компания';

        $mock->expects($this->any())->method('apiAdd')
            ->will($this->returnValueMap([
                // last arg is return value
                [[], 100],
                [[$mock, $mock], [100, 200]],
            ]));

        $this->assertEquals(100, $mock->apiAdd());
        $this->assertCount(2, $mock->apiAdd([$mock, $mock]));
    }

    public function testApiUpdate()
    {
        $mock = $this->getMockBuilder('\AmoCRM\Models\Company')
            ->setConstructorArgs([new \AmoCRM\Request\ParamsBag()])
            ->setMethods(['apiUpdate'])
            ->getMock();

        $this->assertInstanceOf('\AmoCRM\Models\Company', $mock);

        $mock['name'] = 'ООО Тестовая компания';

        $mock->expects($this->any())->method('apiUpdate')
            ->will($this->returnValue(true));

        $this->assertTrue($mock->apiUpdate(1));
        $this->assertTrue($mock->apiUpdate(1, 'now'));
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['name', 'Компания', 'Компания'],
            ['request_id', 100, 100],
            ['date_create', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['last_modified', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['responsible_user_id', 100, 100],
            ['linked_leads_id', 100, [100]],
            ['linked_leads_id', [100, 200], [100, 200]],
            ['tags', 'Tag', 'Tag'],
            ['tags', ['Tag 1', 'Tag 2'], 'Tag 1,Tag 2'],
        ];
    }
}
