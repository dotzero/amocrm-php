<?php

class NoteMock extends \AmoCRM\Models\Note
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['notes' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'notes' => [
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

class NoteTest extends TestCase
{
    /**
     * @var null|NoteMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new NoteMock($paramsBag);
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
            'query' => 'test',
        ];

        $result = $this->model->apiList($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/notes/list', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiAdd()
    {
        $this->model['element_id'] = 100;
        $this->model['element_type'] = 1;
        $this->model['note_type'] = 4;
        $this->model['text'] = 'Текст примечания';

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertEquals('/private/api/v2/json/notes/set', $this->model->mockUrl);
        $this->assertNull($this->model->mockModified);

        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
        $this->assertEquals('/private/api/v2/json/notes/set', $this->model->mockUrl);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUpdate()
    {
        $this->model['element_id'] = 100;
        $this->model['element_type'] = 1;
        $this->model['note_type'] = 4;
        $this->model['text'] = 'Текст примечания';

        $this->assertTrue($this->model->apiUpdate(1));
        $this->assertEquals('/private/api/v2/json/notes/set', $this->model->mockUrl);
        $this->assertNull($this->model->mockModified);

        $this->assertTrue($this->model->apiUpdate(1, 'now'));
        $this->assertEquals('/private/api/v2/json/notes/set', $this->model->mockUrl);
        $this->assertNull($this->model->mockModified);
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['element_id', 100, 100],
            ['element_type', 100, 100],
            ['note_type', 100, 100],
            ['date_create', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['last_modified', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['request_id', 100, 100],
            ['text', "Line 1\nLine 2", "Line 1\nLine 2"],
            ['responsible_user_id', 100, 100],
            ['created_user_id', 100, 100],
        ];
    }
}
