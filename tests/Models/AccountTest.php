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

        return ['account' => [
            'users' => [
                [
                    'id' => 1,
                    'login' => 'mail@example.com',
                ],
                [
                    'id' => 2,
                    'login' => 'foo@bar.com',
                ]
            ]
        ]];
    }
}

class AccountTest extends TestCase
{
    /**
     * @var null|AccountMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $paramsBag->addAuth('login', 'mail@example.com');
        $this->model = new AccountMock($paramsBag);
    }

    public function testApiCurrent()
    {
        $parameters = [
            'free_users' => 'Y'
        ];

        $result = $this->model->apiCurrent(false, $parameters);

        $this->assertNotEmpty($result);
        $this->assertEquals('/private/api/v2/json/accounts/current', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testGetUserByLogin()
    {
        $actual = $this->model->getUserByLogin();
        $this->assertNotEmpty($actual);
        $this->assertArrayHasKey('id', $actual);
        $this->assertEquals('mail@example.com', $actual['login']);

        $actual = $this->model->getUserByLogin('foo@bar.com');
        $this->assertNotEmpty($actual);
        $this->assertArrayHasKey('id', $actual);
        $this->assertEquals('foo@bar.com', $actual['login']);
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
}
