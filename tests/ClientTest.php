<?php

class ClientTest extends PHPUnit_Framework_TestCase
{
    private $amo = null;

    public function setUp()
    {
        $this->amo = new \AmoCRM\Client('example.com', 'login', 'hash');
    }

    /**
     * @dataProvider modelsProvider
     */
    public function testGetModel($model, $expected)
    {
        $model = $this->amo->{$model};

        $this->assertInstanceOf($expected, $model);
    }

    /**
     * @expectedException \AmoCRM\ModelException
     */
    public function testIncorrectModel()
    {
        $this->amo->foobar;
    }

    public function testHelperFields()
    {
        $this->assertInstanceOf('\AmoCRM\Helpers\Fields', $this->amo->fields);
    }

    public function modelsProvider()
    {
        return [
            // model name, expected
            ['account', '\AmoCRM\Models\Account'],
            ['company', '\AmoCRM\Models\Company'],
            ['contact', '\AmoCRM\Models\Contact'],
            ['lead', '\AmoCRM\Models\Lead'],
            ['note', '\AmoCRM\Models\Note'],
            ['task', '\AmoCRM\Models\Task'],
        ];
    }
}
