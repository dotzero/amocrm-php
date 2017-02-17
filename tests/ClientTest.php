<?php

class ClientTest extends TestCase
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
            ['customers_periods', '\AmoCRM\Models\CustomersPeriods'],
            ['lead', '\AmoCRM\Models\Lead'],
            ['note', '\AmoCRM\Models\Note'],
            ['task', '\AmoCRM\Models\Task'],
            ['pipelines', '\AmoCRM\Models\Pipelines'],
            ['unsorted', '\AmoCRM\Models\Unsorted'],
            ['widgets', '\AmoCRM\Models\Widgets'],
            ['webhooks', '\AmoCRM\Models\WebHooks'],
        ];
    }
}
