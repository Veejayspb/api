<?php

namespace veejay\api;

use OpenApi\Generator;
use Throwable;
use veejay\api\component\Code;
use veejay\api\component\Exception;
use veejay\api\component\Request;
use veejay\api\component\Route;
use veejay\api\component\View;
use veejay\api\response\Response;
use veejay\api\response\ResponseJson;

class App
{
    /**
     * Название API.
     * Выводится в заголовке страницы с документацией.
     * @var string
     */
    public string $name = 'Test API';

    /**
     * Объект для работы с запросом.
     * @var Request
     */
    public Request $request;

    /**
     * Объект для работы с ответом.
     * @var Response
     */
    public Response $response;

    /**
     * Список роутов.
     * @var Route[]
     */
    protected array $routes = [];

    /**
     * Экземпляр приложения.
     * @var static
     */
    protected static self $instance;

    protected function __construct()
    {
        $this->request = new Request;
        $this->response = new ResponseJson;
    }

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
     * Добавить роут.
     * @param string $path - адрес
     * @param string $method - название метода
     * @param callable $callback - функция для вывода результата
     * @return static
     */
    public function addRoute(string $path, string $method, callable $callback): static
    {
        $this->routes[] = new Route($path, $method, $callback);
        return $this;
    }

    /**
     * Добавить роуты.
     * @param array $items
     * @return static
     */
    public function addRoutes(array $items): static
    {
        foreach ($items as $item) {
            $this->addRoute(
                $item['path'] ?? null,
                $item['method'] ?? null,
                $item['callback'] ?? null,
            );
        }

        return $this;
    }

    /**
     * Запуск приложения.
     * @return string
     */
    public function run(): string
    {
        try {
            $route = $this->determineRoute();
            $path = $this->request->getPath();
            $allowedMethods = $this->getAllowedMethods($path);

            // Вывод в заголовке допустимых методов для данного адреса
            if ($allowedMethods) {
                $this->response->addHeader('Allow', implode(', ', $allowedMethods));
            }

            if (!$route) {
                throw new Exception(
                    $allowedMethods ? 'Method not allowed' : 'Page not found',
                    $allowedMethods ? Code::NOT_ALLOWED : Code::NOT_FOUND
                );
            }

            $params = $route->extractParams($this->request);
            $_GET = $params + $_GET;
            $this->response->body = $route->runCallback();
        } catch (Exception $exception) {

            $exception->prepareResponse($this->response);

        } catch (Throwable $exception) {

            $exception = new Exception($exception->getMessage(), Code::INTERNAL_SERVER_ERROR, $exception);
            $exception->prepareResponse($this->response);

        }

        return $this->response->run();
    }

    /**
     * Сгенерировать HTML шаблон для документации.
     * @param string $yamlUrl - адрес YAML документа.
     * @return string
     */
    public function swaggerHtml(string $yamlUrl): string
    {
        header('Content-Type: text/html; charset=utf-8');
        $path = __DIR__ . '/template/swagger.php';

        return (new View)->render($path, [
            'name' => $this->name,
            'yamlUrl' => $yamlUrl,
        ]);
    }

    /**
     * Сгенерировать YAML документ на основе спец.комментариев к коду.
     * @param string $path - путь до директории с приложением
     * @return string
     */
    public function swaggerYaml(string $path): string
    {
        header('Content-Type: application/x-yaml');
        return Generator::scan([$path])->toYaml();
    }

    /**
     * Определить какой роут соответствует текущему запросу.
     * @return Route|null
     */
    protected function determineRoute(): ?Route
    {
        foreach ($this->routes as $route) {
            $compare = $route->compare($this->request);

            if ($compare) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Список доступных методов для указанного адреса.
     * @param string $path
     * @return array
     */
    protected function getAllowedMethods(string $path): array
    {
        $methods = [];

        foreach ($this->routes as $route) {
            $compare = $route->comparePath($path);

            if ($compare) {
                $methods[] = $route->method;
            }
        }

        return array_unique($methods);
    }
}
