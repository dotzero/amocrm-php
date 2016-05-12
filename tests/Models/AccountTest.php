<?php

class AccountMock extends \AmoCRM\Models\Account
{
    protected function getRequest($url, $parameters = [], $modified = null)
    {
        return ['account' => []];
    }
}

class AccountTest extends PHPUnit_Framework_TestCase
{
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new AccountMock($paramsBag);
    }

    public function testApiCurrent()
    {
        $this->assertEquals([], $this->model->apiCurrent());
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
