<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Controller;
use veejay\api\request\Request;
use veejay\api\router\Rule;

final class RuleTest extends TestCase
{
    public function testConstruct()
    {
        $pattern = '/some/pattern';
        $controller = 'controller';
        $action = 'action';
        $method = 'GET';

        $rule = new Rule($pattern, $controller, $action, $method);

        $this->assertSame($pattern, $rule->pattern);
        $this->assertSame($controller, $rule->controller);
        $this->assertSame($action, $rule->action);
        $this->assertSame($method, $rule->method);
    }

    public function testCompare()
    {
        $items = [
            [
                'pattern' => '/user/{id}', // Без шаблона
                'method' => 'GET',
                'result' => true,
            ],
            [
                'pattern' => '/user/{id:[0-9]+}', // С шаблоном
                'method' => 'GET',
                'result' => true,
            ],
            [
                'pattern' => '/user/{id:[a-z]+}', // Неверный шаблон
                'method' => 'GET',
                'result' => false,
            ],
            [
                'pattern' => '/user/{id}',
                'method' => 'POST', // Неверный метод
                'result' => false,
            ],
        ];

        $request = $this->getRequest();
        $request->path = '/user/123';
        $request->method = 'GET';

        foreach ($items as $item) {
            $rule = new Rule($item['pattern'], 'controller', 'action', $item['method']);
            $actual = $rule->compare($request);
            $this->assertSame($item['result'], $actual);
        }
    }

    public function testGetParams()
    {
        $rule = new Rule('/user/id-{id}/name-{name:[a-z]+}', 'controller', 'action', 'GET');
        $params = $rule->getParams('/user/id-123/name-any');

        $this->assertSame(['id' => '123', 'name' => 'any'], $params);
    }

    public function testRun()
    {
        $rule = new Rule('', TestController::class, 'index');

        $actual = $rule->run(['somename']);
        $this->assertSame(['index', 'somename'], $actual);

        $actual = $rule->run([]);
        $this->assertSame(['index', null], $actual);
    }

    public function testRunInvalidController()
    {
        $this->expectExceptionCode(500);
        $rule = new Rule('', 'UNDEFINED', 'index');
        $rule->run([]);
    }

    public function testRunInvalidAction()
    {
        $this->expectExceptionCode(404);
        $rule = new Rule('', TestController::class, 'UNDEFINED');
        $rule->run([]);
    }

    public function testRunNoAccess()
    {
        $this->expectExceptionCode(403);
        $rule = new Rule('', TestController::class, 'forbidden');
        $rule->run([]);
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return new class extends Request
        {
            public string $path;
            public string $method;

            public function getPath(): string
            {
                return $this->path;
            }

            public function getMethod(): string
            {
                return $this->method;
            }
        };
    }
}

final class TestController extends Controller
{
    public function _access(string $action): bool
    {
        return $action != 'forbidden';
    }

    public function index(?string $name = null): array
    {
        return ['index', $name];
    }

    public function forbidden(): array
    {
        return ['forbidden'];
    }
}
