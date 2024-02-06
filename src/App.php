<?php

namespace veejay\api;

use Throwable;
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
     * Объект запроса.
     * @var Request
     */
    protected Request $request;

    /**
     * Объект ответа.
     * @var Response
     */
    protected Response $response;

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
     * @return mixed
     */
    public function __get(string $name)
    {
        return match ($name) {
            'request' => $this->request = $this->request ?? $this->getRequest(),
            'response' => $this->response = $this->response ?? $this->getResponse(),
            default => $this->$name,
        };
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
            $rule = $router->getRule();

            if (!$rule) {
                throw new Exception('Method not found', 404);
            }

            $data = $rule->run();

            if ($data === null) {
                throw new Exception('Method not found', 404);
            }

            $response->data = $data;
        } catch (Exception $e) {
            $e->prepareResponse($response);
        } catch (Throwable $e) {
            $exception = new Exception($e->getMessage(), 500, $e);
            $exception->prepareResponse($response);
        }

        echo $response->run();
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
