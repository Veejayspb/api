<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Request;
use veejay\api\component\Route;

final class RouteTest extends TestCase
{
    public function testConstruct()
    {
        $route = new Route('/user', 'put', $callback = function () {return null;});

        $this->assertSame('/user', $route->path);
        $this->assertSame('PUT', $route->method);
        $this->assertSame($callback, $route->callback);
    }

    public function testRunCallback()
    {
        $route = new Route('/', Request::PATCH, function () {
            return null;
        });
        $actual = $route->runCallback();
        $this->assertNull($actual);

        $route = new Route('/', Request::PATCH, function () {
            return ['content'];
        });
        $actual = $route->runCallback();
        $this->assertSame(['content'], $actual);
    }

    public function testComparePath()
    {
        $route = new Route('/user/{id}', Request::PATCH, function () {
            return null;
        });

        $items = [
            '/user/1' => true,
            '/user/one' => true,
            '/users/1' => false,
            '/user/1/one' => false,
        ];

        foreach ($items as $path => $expected) {
            $actual = $route->comparePath($path);
            $this->assertSame($expected, $actual);
        }
    }

    public function testCompare()
    {
        $request = new class extends Request {};
        $route = new Route('/user/{id}', Request::PATCH, function () {return null;});

        $_SERVER['REQUEST_METHOD'] = Request::PATCH;
        $_SERVER['REQUEST_URI'] = '/user/123?q=1';
        $actual = $route->compare($request);
        $this->assertTrue($actual);

        $_SERVER['REQUEST_METHOD'] = Request::POST;
        $_SERVER['REQUEST_URI'] = '/user/123?q=1';
        $actual = $route->compare($request);
        $this->assertFalse($actual);

        $_SERVER['REQUEST_METHOD'] = Request::PATCH;
        $_SERVER['REQUEST_URI'] = '/user';
        $actual = $route->compare($request);
        $this->assertFalse($actual);
    }

    public function testExtractParams()
    {
        $request = new class extends Request {};
        $route = new Route('/valid/{id}', Request::PATCH, function () {return null;});

        $_SERVER['REQUEST_URI'] = '/valid/123?q=1';
        $actual = $route->extractParams($request);
        $this->assertSame(['id' =>'123'], $actual);

        $_SERVER['REQUEST_URI'] = '/invalid/123';
        $actual = $route->extractParams($request);
        $this->assertSame([], $actual);
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return new class extends Request
        {
            public function getHeaders(): array
            {
                return [];
            }
        };
    }
}
