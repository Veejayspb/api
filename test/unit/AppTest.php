<?php

use veejay\api\component\Request;
use veejay\api\component\Route;
use veejay\test\component\App;
use veejay\test\component\TestCase;

class AppTest extends TestCase
{
    const USER_DATA_1 = [
        'id' => 1,
        'name' => 'User one',
    ];

    const USER_DATA_2 = [
        'id' => 2,
        'name' => 'User two',
    ];

    public function testInstance()
    {
        $this->assertSame(App::instance(), App::instance());
    }

    public function testRun()
    {
        $app = App::instance();

        $_SERVER['REQUEST_URI'] = '/';
        $actual = $app->run();
        $this->assertSame('documentation', $actual);

        $_SERVER['REQUEST_URI'] = '/some/address';
        $actual = $app->run();
        $this->assertSame('data', $actual);
    }

    public function testGetRequest()
    {
        $app = App::instance();
        $this->assertSame($app->getRequest(), $app->getRequest());
    }

    public function testGetResponse()
    {
        $app = App::instance();
        $this->assertSame($app->getResponse(), $app->getResponse());
    }

    public function testRegisterRoutes()
    {
        $items = [
            [
                'description' => 'Users list',
                'uri' => '/v1/user',
                'method' => Request::GET,
                'callback' => function () {
                    return [self::USER_DATA_1, self::USER_DATA_2];
                },
                'params' => [],
                'required' => [],
                'returns' => [self::USER_DATA_1, self::USER_DATA_2],
            ],
            [
                'description' => 'Update user',
                'uri' => '/v1/user/{id}',
                'method' => Request::POST,
                'callback' => function () {
                    return self::USER_DATA_1;
                },
                'params' => [
                    'name' => 'User name'
                ],
                'required' => [
                    'name',
                ],
                'returns' => self::USER_DATA_1,
            ],
        ];

        $app = App::instance();
        $app->registerRoutes($items);

        $this->assertArrayHasKey(0, $app->routes);
        $this->assertArrayHasKey(1, $app->routes);
        $this->assertArrayNotHasKey(2, $app->routes);
        $this->assertEquals(new Route($app, $items[0]), $app->routes[0]);
        $this->assertEquals(new Route($app, $items[1]), $app->routes[1]);
    }
}
