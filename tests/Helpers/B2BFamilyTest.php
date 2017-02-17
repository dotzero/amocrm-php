<?php

class B2BFamilyMock extends \AmoCRM\Helpers\B2BFamily
{
    protected function request($method, $url, $parameters = [])
    {
        return $parameters;
    }
}

class B2BFamilyTest extends TestCase
{
    /**
     * @var null|\AmoCRM\Helpers\B2BFamily
     */
    private $b2b = null;

    public function setUp()
    {
        $amo = new \AmoCRM\Client('example.com', 'login', 'hash');
        $this->b2b = new B2BFamilyMock(
            $amo,
            'b2b_appkey',
            'b2b_secret',
            'b2b_email',
            'b2b_password'
        );
    }

    public function testLogin()
    {
        $response = $this->b2b->login();

        $this->assertEquals($response['appkey'], 'b2b_appkey');
        $this->assertEquals($response['email'], 'b2b_email');
        $this->assertEquals($response['password'], 'b2b_password');
        $this->assertEquals($response['hash'], 'fde29cb7e620a15fbf23b75c61725a7e');
    }

    public function testSubscribe()
    {
        $response = $this->b2b->subscribe();

        $this->assertArrayHasKey('apikey', $response);
        $this->assertEquals($response['path'], 'http://b2bf.cloudapp.net/post/');
    }

    public function testUnsubscribe()
    {
        $response = $this->b2b->unsubscribe(100);

        $this->assertArrayHasKey('apikey', $response);
        $this->assertEquals($response['id'], 100);
    }

    public function testMail()
    {
        $response = $this->b2b->mail(100, ['foobar' => ['foo' => 'bar']]);

        $this->assertArrayHasKey('apikey', $response);
        $this->assertArrayHasKey('custom_data', $response);
        $this->assertArrayHasKey('notification_settings', $response);
        $this->assertEquals($response['foobar']['foo'], 'bar');
        $this->assertEquals($response['custom_data']['userDomainAmo'], 'example.com');
        $this->assertEquals($response['custom_data']['userLoginAmo'], 'login');
        $this->assertEquals($response['custom_data']['userHashAmo'], 'hash');
        $this->assertEquals($response['custom_data']['userTypeAmo'], 2);
        $this->assertEquals($response['custom_data']['userIdAmo'], 100);
        $this->assertFalse($response['notification_settings']['sms_enable']);
        $this->assertFalse($response['notification_settings']['email_enable']);
        $this->assertTrue($response['notification_settings']['webhook_enable']);
    }
}
