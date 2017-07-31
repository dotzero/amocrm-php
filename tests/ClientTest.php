<?php

class ClientTest extends TestCase
{
    private $amo = null;

    public function setUp()
    {
        $this->amo = new \AmoCRM\Client('example', 'login', 'hash');
    }

    public function testSubDomain()
    {
        $amo = new \AmoCRM\Client('test', 'login', 'hash');

        $this->assertEquals('test.amocrm.ru', $amo->parameters->getAuth('domain'));
        $this->assertCount(3, $amo->parameters->getAuth());
    }

    public function testFullDomain()
    {
        $amo = new \AmoCRM\Client('test-ru.amocrm.ru', 'login', 'hash');

        $this->assertEquals('test-ru.amocrm.ru', $amo->parameters->getAuth('domain'));
        $this->assertCount(3, $amo->parameters->getAuth());
    }

    public function testFullDomainCom()
    {
        $amo = new \AmoCRM\Client('test-com.amocrm.com', 'login', 'hash');

        $this->assertEquals('test-com.amocrm.com', $amo->parameters->getAuth('domain'));
        $this->assertCount(3, $amo->parameters->getAuth());
    }

    /**
     * @dataProvider modelsProvider
     */
    public function testGetModel($name, $expected)
    {
        $model = $this->amo->{$name};

        $this->assertInstanceOf($expected, $model);
        $this->assertInstanceOf('\AmoCRM\Models\ModelInterface', $model);
        $this->assertSame($expected, (string)$model);
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
            ['account', 'AmoCRM\Models\Account'],
            ['call', 'AmoCRM\Models\Call'],
            ['catalog', 'AmoCRM\Models\Catalog'],
            ['catalog_element', 'AmoCRM\Models\CatalogElement'],
            ['company', 'AmoCRM\Models\Company'],
            ['contact', 'AmoCRM\Models\Contact'],
            ['customer', 'AmoCRM\Models\Customer'],
            ['customers_periods', 'AmoCRM\Models\CustomersPeriods'],
            ['custom_field', 'AmoCRM\Models\CustomField'],
            ['lead', 'AmoCRM\Models\Lead'],
            ['links', 'AmoCRM\Models\Links'],
            ['note', 'AmoCRM\Models\Note'],
            ['pipelines', 'AmoCRM\Models\Pipelines'],
            ['task', 'AmoCRM\Models\Task'],
            ['transaction', 'AmoCRM\Models\Transaction'],
            ['unsorted', 'AmoCRM\Models\Unsorted'],
            ['webhooks', 'AmoCRM\Models\Webhooks'],
            ['widgets', 'AmoCRM\Models\Widgets'],
        ];
    }
}
