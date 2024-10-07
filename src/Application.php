<?php

namespace veejay\api;

use Throwable;
use veejay\api\component\Exception;
use veejay\api\component\Request;
use veejay\api\component\Route;
use veejay\api\component\View;
use veejay\api\response\Response;
use veejay\api\response\ResponseJson;

class Application
{
    /**
     * Базовый URI.
     * @var string
     */
    public string $baseUri = '';

    /**
     * Название API.
     * @var string
     */
    public string $name = 'API doc';

    /**
     * Список роутов.
     * @var Route[]
     */
    public array $routes = [];

    /**
     * Объект для работы с запросом.
     * @var Request
     */
    protected Request $request;

    /**
     * Объект для работы с ответом.
     * @var Response
     */
    protected Response $response;

    /**
     * Экземпляр приложения.
     * @var static
     */
    protected static self $instance;

    private function __construct(){}

    private function __clone() {}

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
     * @return string
     */
    public function run(): string
    {
        $uri = $this->getRequest()->getUri();
        $docUri = $this->getDocUri();

        if ($uri == $docUri) {
            return $this->documentation();
        } else {
            return $this->data();
        }
    }

    /**
     * Вернуть объект для работы с запросом.
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request = $this->request ?? new Request;
    }

    /**
     * Вернуть объект для работы с ответом.
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response = $this->response ?? new ResponseJson;
    }

    /**
     * Регистрация роутов из массива данных.
     * @param array $config
     * @return void
     */
    public function registerRoutes(array $config): void
    {
        foreach ($config as $item) {
            $this->routes[] = new Route($this, $item);
        }
    }

    /**
     * Вернуть данные от API.
     * @return string
     */
    protected function data(): string
    {
        $response = $this->getResponse();
        $request = $this->getRequest();

        try {
            $route = $this->determineRoute();
            $payload = $request->getHeaderPayload();

            if (!$route) {
                throw new Exception('Page not found', 404);
            }

            if (!is_callable($route->callback)) {
                throw new Exception('Invalid route callback', 500);
            }

            if (!$route->checkRequired($payload)) {
                throw new Exception('Required params: ' . implode(', ', $route->required), 400);
            }

            $params = $route->extractParams($request);
            $_GET = $params + $_GET;

            $result = $route->runCallback();
            $response->data = $result;

        } catch (Exception $e) {

            $e->prepareResponse($response);

        } catch (Throwable $e) {

            $exception = new Exception($e->getMessage(), 500, $e);
            $exception->prepareResponse($response);

        }

        return $response->run();
    }

    /**
     * Вернуть страницу с документацией.
     * @return string
     */
    protected function documentation(): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = __DIR__ . $ds . 'template' . $ds . 'main.php';

        return (new View)->render($path, [
            'application' => $this,
        ]);
    }

    /**
     * Определить какой роут соответствует текущему запросу.
     * @return Route|null
     */
    protected function determineRoute(): ?Route
    {
        $request = $this->getRequest();

        foreach ($this->routes as $route) {
            $compare = $route->compare($request);

            if ($compare) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Вернуть URI, на котором выводится документация.
     * @return string
     */
    protected function getDocUri(): string
    {
        return $this->baseUri == '' ? '/' : $this->baseUri;
    }
}
