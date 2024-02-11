<?php

namespace veejay\api;

use Throwable;
use veejay\api\component\Component;
use veejay\api\component\Exception;
use veejay\api\request\Request;
use veejay\api\response\Response;
use veejay\api\response\ResponseJson;
use veejay\api\router\Router;

/**
 * Class App
 * @property-read Request $request
 * @property-read Response $response
 */
class App
{
    /**
     * Список инстанцированных компонентов.
     * @var Component[]
     */
    protected array $components = [];

    /**
     * Индикатор запуска приложения.
     * @var bool
     */
    protected bool $running = false;

    /**
     * Экземпляр приложения.
     * @var static
     */
    protected static self $instance;

    protected function __construct() {}

    /**
     * @param string $name
     * @return Component
     * @throws Exception
     */
    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->components)) {
            $component = $this->getComponent($name);

            if (!$component) {
                throw new Exception("Component not found: $name", 500);
            }

            $this->components[$name] = $component;
        }

        return $this->components[$name];
    }

    /**
     * Вернуть экземпляр приложения.
     * @return static
     */
    public static function instance(): static
    {
        return static::$instance = static::$instance ?? new static;
    }

    /**
     * Запуск приложения.
     * @param array $rules - правила роутинга
     * @return void
     */
    public function run(array $rules): void
    {
        if ($this->running) {
            return;
        } else {
            $this->running = true;
        }

        $request = $this->__get('request');
        $response = $this->__get('response');

        try {
            $method = $request->getMethod();

            if ($method === Request::OTHER) {
                throw new Exception('Unknown request method', 400);
            }

            $router = $this->getRouter($rules);
            $rule = $router->findRule();

            if (!$rule) {
                throw new Exception('Method not found', 404);
            }

            $params = $rule->getParams($request->getPath());
            $response->data = $rule->run($params);
        } catch (Exception $e) {
            $e->prepareResponse($response);
        } catch (Throwable $e) {
            $exception = new Exception($e->getMessage(), 500, $e);
            $exception->prepareResponse($response);
        }

        echo $response->run();
    }

    /**
     * Создать объект указанного компонента.
     * @param string $name - название компонента
     * @return Component|null
     */
    protected function getComponent(string $name): ?Component
    {
        $methodName = 'get' . ucfirst($name);

        if (!method_exists($this, $methodName)) {
            return null;
        }

        $component = call_user_func([$this, $methodName]);

        if (!is_subclass_of($component, Component::class)) {
            return null;
        }

        return $component;
    }

    /**
     * Вернуть объект запроса.
     * @return Request
     */
    protected function getRequest(): Request
    {
        return new Request;
    }

    /**
     * Вернуть объект ответа.
     * @return Response
     */
    protected function getResponse(): Response
    {
        return new ResponseJson;
    }

    /**
     * Вернуть роутер.
     * @param array $rules - правила роутинга
     * @return Router
     */
    protected function getRouter(array $rules): Router
    {
        $request = $this->__get('request');
        return new Router($request, $rules);
    }
}
