<?php

use PHPUnit\Framework\TestCase;
use veejay\api\App;
use veejay\api\component\Controller;
use veejay\api\request\Request;

final class AppTest extends TestCase
{
    public function testGet()
    {
        $app = $this->getApp();

        // При каждом вызове должен возвращаться тот же объект
        $this->assertSame($app->request, $app->request);
        $this->assertSame($app->response, $app->response);
    }

    public function testInstance()
    {
        $app1 = App::instance();
        $app2 = App::instance();

        $this->assertSame($app1, $app2);
    }

    public function testRun()
    {
        $content = $this->getOutput(function () {
            $app = $this->getApp();
            $app->request->method = Request::GET;
            $app->request->uri = '/some/address';
            $app->run([
                ['/some/address', TestController::class, 'index', Request::GET],
            ]);
        });

        $this->assertSame('["test\/index"]', $content);
    }

    public function testRunWrongMethod()
    {
        $content = $this->getOutput(function () {
            $app = $this->getApp();
            $app->request->method = Request::OTHER;
            $app->run([]);
        });

        $data = json_decode($content, true);
        $this->assertSame(400, $data['code']);
    }

    public function testRunNoRule()
    {
        $content = $this->getOutput(function () {
            $app = $this->getApp();
            $app->request->method = Request::GET;
            $app->run([]);
        });

        $data = json_decode($content, true);
        $this->assertSame(404, $data['code']);
    }

    /**
     * @return App
     */
    protected function getApp()
    {
        return new class extends App
        {
            public function __construct()
            {
                parent::__construct();
            }

            protected function getRequest(): Request
            {
                return new class extends Request
                {
                    public string $method;
                    public string $uri;

                    public function getMethod(): string
                    {
                        return $this->method;
                    }

                    public function getUri(): string
                    {
                        return $this->uri;
                    }
                };
            }
        };
    }

    /**
     * Сохранение в буфер и возврат вместо прямого вывода.
     * @param callable $callback
     * @return string
     */
    protected function getOutput(callable $callback): string
    {
        ob_start();
        call_user_func($callback);
        return ob_get_clean();
    }
}

class TestController extends Controller
{
    public function index(): array
    {
        return ['test/index'];
    }
}
