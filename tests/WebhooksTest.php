<?php

class WebhooksTest extends PHPUnit_Framework_TestCase
{
    private $listener = null;

    private $fixtureAdd = [
        'contacts' => [
            'add' => [
                [
                    'id' => 12254016,
                    'name' => 'тест',
                    'responsible_user_id' => 798027,
                    'group_id' => 44503,
                    'date_create' => 1463058053,
                    'last_modified' => 1463058053,
                    'created_user_id' => 798027,
                    'modified_user_id' => 798027,
                    'type' => 'contact',
                ]
            ]
        ],
        'account' => [
            'subdomain' => 'example'
        ],
    ];

    private $fixtureDelete = [
        'contacts' => [
            'delete' => [
                [
                    'id' => 12254016,
                    'type' => 'contact'
                ]
            ]
        ],
        'account' => [
            'subdomain' => 'example'
        ],
    ];

    public function setUp()
    {
        $this->listener = new \AmoCRM\Webhooks();
    }

    public function testOn()
    {
        $this->listener->on('contacts-add', function ($domain, $id, $data) {

        });
        $this->assertAttributeCount(1, 'hooks', $this->listener);

        $this->listener->on(['contacts-update', 'contacts-delete'], [$this, 'testOn']);
        $this->assertAttributeCount(3, 'hooks', $this->listener);
    }

    /**
     * @expectedException \AmoCRM\Exception
     */
    public function testIncorrectCallback()
    {
        $this->listener->on('contacts-add', true);
    }

    public function testListenOne()
    {
        $this->assertFalse($this->listener->listen());

        $_POST = $this->fixtureAdd;

        $this->listener->on('contacts-add', [$this, 'addCallback']);
        $this->listener->listen();
    }

    public function testListenMulti()
    {
        $_POST = $this->fixtureAdd;

        $this->listener->on('contacts-add', [$this, 'addCallback']);
        $this->listener->listen();

        $_POST = $this->fixtureDelete;

        $this->listener->on(['contacts-delete', 'contacts-update'], [$this, 'updateCallback']);
        $this->listener->listen();
    }

    public function addCallback($domain, $id, $data)
    {
        $this->assertEquals('example', $domain);
        $this->assertEquals(12254016, $id);
        $this->assertNotEmpty($data);
        $this->assertCount(9, $data);
    }

    public function updateCallback($domain, $id, $data)
    {
        $this->assertEquals('example', $domain);
        $this->assertEquals(12254016, $id);
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);
    }
}
