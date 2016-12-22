<?php

class WebhooksTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|\AmoCRM\Webhooks
     */
    private $listener = null;
    private $fired = false;

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
            // pass
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
        $this->fired = false;
        $_POST = $this->fixtureAdd;

        $this->listener
            ->on('contacts-add', [$this, 'addCallback'])
            ->listen();

        $this->assertTrue($this->fired);
    }

    public function testListenMulti()
    {
        $this->fired = false;
        $_POST = $this->fixtureAdd;

        $this->listener
            ->on('contacts-add', [$this, 'addCallback'])
            ->listen();

        $this->assertTrue($this->fired);

        $this->fired = false;
        $_POST = $this->fixtureDelete;

        $this->listener
            ->on(['contacts-delete', 'contacts-update'], [$this, 'updateCallback'])
            ->listen();

        $this->assertTrue($this->fired);
    }

    public function testEmptyListen()
    {
        $this->assertFalse($this->listener->listen());
    }

    public function addCallback($domain, $id, $data)
    {
        $this->fired = true;
        $this->assertEquals('example', $domain);
        $this->assertEquals(12254016, $id);
        $this->assertNotEmpty($data);
        $this->assertCount(9, $data);
    }

    public function updateCallback($domain, $id, $data)
    {
        $this->fired = true;
        $this->assertEquals('example', $domain);
        $this->assertEquals(12254016, $id);
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);
    }
}
