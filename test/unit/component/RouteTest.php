<?php

use veejay\api\App;
use veejay\api\component\Request;
use veejay\api\component\Route;
use veejay\test\component\TestCase;

class RouteTest extends TestCase
{
    const PATTERN_CORRECT = '/v{version}/user/{id}';
    const PATTERN_WRONG = '/wrong/pattern/{id}';

    public function testGetUri()
    {
        $application = App::instance();
        $route = new Route($application, []);

        $route->uri = '/user/{id}';
        $this->assertSame('/user/{id}', $route->getUri());

        $application->baseUri = '/v1';
        $this->assertSame('/v1/user/{id}', $route->getUri());
    }

    public function testCompare()
    {
        $request = $this->getRequest();
        $route = $this->getRoute();

        $route->uri = self::PATTERN_CORRECT;
        $actual = $route->compare($request);
        $this->assertTrue($actual);

        $route->uri = self::PATTERN_WRONG;
        $actual = $route->compare($request);
        $this->assertFalse($actual);
    }

    public function testExtractParams()
    {
        $request = $this->getRequest();
        $route = $this->getRoute();

        $route->uri = self::PATTERN_CORRECT;
        $actual = $route->extractParams($request);
        $this->assertSame(['version' => 1, 'id' => 123], $actual);

        $route->uri = self::PATTERN_WRONG;
        $this->assertSame([], $route->extractParams($request));
    }

    public function testRunCallback()
    {
        $route = $this->getRoute();
        $route->callback = function () {
            return ['result'];
        };
        $actual = $route->runCallback();

        $this->assertSame(['result'], $actual);
    }

    public function testGetRequired()
    {
        $route = $this->getRoute();

        $this->assertSame(['b'], $route->getRequired());
    }

    public function testCheckRequired()
    {
        $route = $this->getRoute();

        $this->assertTrue($route->checkRequired([
            'a' => 'aaa',
            'b' => 'bbb',
        ]));

        $this->assertFalse($route->checkRequired([
            'a' => 'aaa',
        ]));
    }

    public function testIsRequired()
    {
        $route = $this->getRoute();

        $this->assertTrue($route->isRequired('a'));
        $this->assertTrue($route->isRequired('b'));
        $this->assertFalse($route->isRequired('c'));
        $this->assertFalse($route->isRequired('d'));
    }

    /**
     * @return Route
     */
    protected function getRoute(): Route
    {
        $application = App::instance();

        $route = new Route($application, []);
        $route->method = Request::POST;
        $route->required = ['a', 'b'];
        $route->params = [
            'b' => 'b',
            'c' => 'c',
        ];

        return $route;
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return new class extends Request
        {
            public function getMethod(): string
            {
                return self::POST;
            }

            public function getPath(): string
            {
                return '/v1/user/123';
            }
        };
    }

    protected function setUp(): void
    {
        App::instance()->baseUri = '';
    }
}
