<?php

use PHPUnit\Framework\TestCase;
use veejay\api\request\Request;
use veejay\api\router\Router;
use veejay\api\router\Rule;

final class RouterTest extends TestCase
{
    /**
     * Правила для REST-контроллера.
     */
    const RULES = [
        ['/user',             'user', 'index',  'GET'],
        ['/user/{id:[0-9]+}', 'user', 'view',   'GET'],
        ['/user',             'user', 'create', 'POST'],
        ['/user',             'user', 'update', 'PATCH'],
        ['/user/{id}',        'user', 'delete', 'DELETE'],
    ];

    public function testConstruct()
    {
        $request = $this->getRequest();
        $router = new Router($request, self::RULES);

        $this->assertSame($request, $router->request);
        $this->assertSame(self::RULES, $router->rules);
    }

    public function testFindRule()
    {
        $items = [
            [
                'uri' => '/user',
                'method' => 'GET',
                'controller' => 'user',
                'action' => 'index',
            ],
            [
                'uri' => '/user?filter[name]=value',
                'method' => 'GET',
                'controller' => 'user',
                'action' => 'index',
            ],
            [
                'uri' => '/user/1',
                'method' => 'GET',
                'controller' => 'user',
                'action' => 'view',
            ],
            [
                'uri' => '/user/abc',
                'method' => 'GET',
                'controller' => null,
                'action' => null,
            ],
            [
                'uri' => '/user',
                'method' => 'POST',
                'controller' => 'user',
                'action' => 'create',
            ],
            [
                'uri' => '/user?name=value',
                'method' => 'POST',
                'controller' => 'user',
                'action' => 'create',
            ],
            [
                'uri' => '/user',
                'method' => 'PATCH',
                'controller' => 'user',
                'action' => 'update',
            ],
            [
                'uri' => '/user?name',
                'method' => 'PATCH',
                'controller' => 'user',
                'action' => 'update',
            ],
            [
                'uri' => '/user/123',
                'method' => 'DELETE',
                'controller' => 'user',
                'action' => 'delete',
            ],
            [
                'uri' => '/user/abc?name=value',
                'method' => 'DELETE',
                'controller' => 'user',
                'action' => 'delete',
            ],
            [
                'uri' => '/user/abc',
                'method' => 'UNDEFINED',
                'controller' => null,
                'action' => null,
                [
                    'uri' => '',
                    'method' => 'GET',
                    'controller' => null,
                    'action' => null,
                ],
            ]
        ];

        $request = $this->getRequest();
        $router = new Router($request, self::RULES);

        foreach ($items as $item) {
            $request->uri = $item['uri'];
            $request->method = $item['method'];
            $rule = $router->findRule(); /* @var Rule $rule */

            if ($item['action'] === null) {
                $this->assertNull($rule);
            } else {
                $this->assertSame(
                    [$item['controller'], $item['action']],
                    [$rule->controller, $rule->action]
                );
            }
        }
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return new class extends Request
        {
            public string $uri = '';
            public string $method = 'GET';

            public function getUri(): string
            {
                return $this->uri;
            }

            public function getMethod(): string
            {
                return $this->method;
            }
        };
    }
}
