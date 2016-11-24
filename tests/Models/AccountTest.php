<?php

class AccountMock extends \AmoCRM\Models\Account
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['account' => []];
    }
}

class AccountTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|AccountMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new AccountMock($paramsBag);
    }

    public function testApiCurrent()
    {
        $parameters = [
            'free_users' => 'Y'
        ];

        $result = $this->model->apiCurrent(false, $parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/accounts/current', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testGetShorted()
    {
        $keys = ['id' => [], 'name' => [], 'login' => [], 'type_id' => [], 'enums' => []];

        $expected = [
            'users' => $keys,
            'leads_statuses' => $keys,
            'note_types' => $keys,
            'task_types' => $keys,
            'custom_fields' => [$keys],
            'pipelines' => $keys,
        ];

        $actual = $this->invokeMethod($this->model, 'getShorted', [$expected]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
