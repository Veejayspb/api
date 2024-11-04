<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Request;
use veejay\api\component\Route;
use veejay\api\test\component\App;

final class AppTest extends TestCase
{
    public function testInstance()
    {
        $this->assertSame(
            App::instance(),
            App::instance()
        );
    }

    public function testAddRoute()
    {
        $app = App::instance()->addRoute('/any/path', Request::PATCH, function () {return 123;});

        $this->assertSame(1, count($app->routes));
        $this->assertArrayHasKey(0, $app->routes);
        $route = $app->routes[0]; /* @var Route $route */
        $this->assertSame('/any/path', $route->path);
        $this->assertSame(Request::PATCH, $route->method);
        $this->assertEquals(123, call_user_func($route->callback));
    }

    public function testAddRoutes()
    {
        $routes = [
            [
                'path' => '/',
                'method' => Request::GET,
                'callback' => function () {return 'g';},
            ],
            [
                'path' => '/any/path',
                'method' => Request::POST,
                'callback' => function () {return 'p';},
            ],
        ];

        $app = App::instance()->addRoutes($routes);

        $this->assertSame(2, count($app->routes));
        $this->assertSame([0, 1], array_keys($app->routes));

        $route = $app->routes[0]; /* @var Route $route */
        $this->assertSame('/', $route->path);
        $this->assertSame(Request::GET, $route->method);
        $this->assertEquals('g', call_user_func($route->callback));

        $route = $app->routes[1]; /* @var Route $route */
        $this->assertSame('/any/path', $route->path);
        $this->assertSame(Request::POST, $route->method);
        $this->assertEquals('p', call_user_func($route->callback));
    }

    public function testRun()
    {
        $routes = [
            [
                'path' => '/a',
                'method' => Request::GET,
                'callback' => function () {return ['a', 'get'];}
            ],
            [
                'path' => '/b',
                'method' => Request::POST,
                'callback' => function () {return ['b', 'post'];}
            ],
        ];
        $app = App::instance()->addRoutes($routes);

        $_SERVER['REQUEST_URI'] = '/a';
        $_SERVER['REQUEST_METHOD'] = Request::GET;
        $this->assertSame('["a","get"]', $app->run());

        $_SERVER['REQUEST_URI'] = '/b';
        $_SERVER['REQUEST_METHOD'] = Request::POST;
        $this->assertSame('["b","post"]', $app->run());

        $_SERVER['REQUEST_URI'] = '/not/found';
        $_SERVER['REQUEST_METHOD'] = Request::GET;
        $this->assertSame('{"code":404,"message":"Page not found"}', $app->run());
    }

    public function testSwaggerHtml()
    {
        $url = '/url/to/swagger.yaml';
        $app = App::instance();
        $html = $app->swaggerHtml($url);

        $this->assertNotFalse(strstr($html, $url));
        $this->assertNotFalse(strstr($html, $app->name));
    }

    public function testSwaggerYaml()
    {
        $projectPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'example';
        $yamlPath = __DIR__ . '/unit/template/swagger.yaml';

        $app = App::instance();
        $yaml = $app->swaggerYaml($projectPath);
        #file_put_contents($yamlPath, $yaml);
        $expected = file_get_contents($yamlPath);
        $this->assertSame($expected, $yaml);
    }

    protected function setUp(): void
    {
        App::instance()->routes = [];
    }
}
