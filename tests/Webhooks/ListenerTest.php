<?php

class ListenerTest extends TestCase
{
    /**
     * @var null|\AmoCRM\Webhooks\Listener
     */
    private $listener = null;

    /**
     * @var bool
     */
    private $fired = false;

    public function setUp()
    {
        $this->fired = false;
        $this->listener = new \AmoCRM\Webhooks\Listener();
    }

    /**
     * @dataProvider eventsProvider
     */
    public function testOn($event, $data)
    {
        $_POST = $data;

        $this->assertFalse($this->fired);
        $this->listener->clean()->on($event, [$this, 'mockCallback'])->listen();
        $this->assertTrue($this->fired);
    }

    public function eventsProvider()
    {
        $events = [];

        foreach (glob(__DIR__ . '/Fixtures/*.json') as $filename) {
            $event = pathinfo($filename, PATHINFO_FILENAME);
            $data = json_decode(file_get_contents($filename), true);
            $events[] = [$event, $data];
        }

        return $events;
    }

    public function mockCallback($domain, $id, $data)
    {
        $this->fired = true;
        $this->assertEquals('example', $domain);
        $this->assertNotEmpty($data);
    }
}