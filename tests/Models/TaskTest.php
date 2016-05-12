<?php

class TaskMock extends \AmoCRM\Models\Task
{
    protected function getRequest($url, $parameters = [], $modified = null)
    {
        return ['tasks' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        return [
            'tasks' => [
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

class TaskTest extends PHPUnit_Framework_TestCase
{
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new TaskMock($paramsBag);
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
        $result = $this->model->apiList([
            'query' => 'test',
        ]);

        $this->assertEquals([], $result);
    }

    public function testApiAdd()
    {
        $this->model['element_id'] = 11029224;
        $this->model['element_type'] = 1;
        $this->model['date_create'] = '-2 DAYS';
        $this->model['task_type'] = 1;
        $this->model['text'] = "Текст\nзадачи";
        $this->model['responsible_user_id'] = 798027;
        $this->model['complete_till'] = '+1 DAY';

        $this->assertEquals(100, $this->model->apiAdd());
        $this->assertCount(2, $this->model->apiAdd([$this->model, $this->model]));
    }

    public function testApiUpdate()
    {
        $this->model['text'] = "Текст\nзадачи";

        $this->assertTrue($this->model->apiUpdate(1, 'апдейт'));
        $this->assertTrue($this->model->apiUpdate(1, 'апдейт', 'now'));
    }

    /**
     * @expectedException \AmoCRM\Exception
     */
    public function testApiUpdateBad()
    {
        $this->model->apiUpdate('foo', 'bar');
    }

    public function fieldsProvider()
    {
        return [
            // field, value, expected
            ['element_id', 100, 100],
            ['element_type', 100, 100],
            ['date_create', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['last_modified', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
            ['request_id', 100, 100],
            ['task_type', 100, 100],
            ['text', "Line 1\nLine 2", "Line 1\nLine 2"],
            ['responsible_user_id', 100, 100],
            ['complete_till', '2016-04-01 00:00:00', strtotime('2016-04-01 00:00:00')],
        ];
    }
}
