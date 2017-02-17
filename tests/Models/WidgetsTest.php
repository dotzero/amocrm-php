<?php

class WidgetsMock extends \AmoCRM\Models\Widgets
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function getRequest($url, $parameters = [], $modified = null)
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = $modified;

        return ['widgets' => []];
    }

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return ['widgets' => []];
    }
}

class WidgetsTest extends TestCase
{
    /**
     * @var null|WidgetsMock
     */
    private $model = null;

    public function setUp()
    {
        $paramsBag = new \AmoCRM\Request\ParamsBag();
        $this->model = new WidgetsMock($paramsBag);
    }

    public function testApiList()
    {
        $parameters = [
            'widget_id' => 62121
        ];

        $result = $this->model->apiList($parameters);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/widgets/list', $this->model->mockUrl);
        $this->assertEquals($parameters, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiInstall()
    {
        $expected = [
            'widgets' => [
                'install' => [
                    'widget_id' => 62121
                ]
            ]
        ];

        $result = $this->model->apiInstall([
            'widget_id' => 62121
        ]);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/widgets/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }

    public function testApiUninstall()
    {
        $expected = [
            'widgets' => [
                'uninstall' => [
                    'widget_id' => 62121
                ]
            ]
        ];

        $result = $this->model->apiUninstall([
            'widget_id' => 62121
        ]);

        $this->assertEquals([], $result);
        $this->assertEquals('/private/api/v2/json/widgets/set', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);
    }
}
