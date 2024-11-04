<?php

namespace veejay\api\component;

class Route
{
    /**
     * Адрес.
     * @var string - /any/path
     */
    public string $path;

    /**
     * Название метода.
     * @var string - GET | POST | PATCH | DELETE | ...
     */
    public string $method;

    /**
     * Функция для вывода результата.
     * @var callable
     */
    public $callback;

    /**
     * @param string $path
     * @param string $method
     * @param callable $callback
     */
    public function __construct(string $path, string $method, callable $callback)
    {
        $this->path = $path;
        $this->method = strtoupper($method);
        $this->callback = $callback;
    }

    /**
     * Запуск коллбэка и возвращение результата.
     * @return array|null
     */
    public function runCallback(): ?array
    {
        return call_user_func($this->callback);
    }

    /**
     * Сравнить адрес.
     * @param string $path
     * @return bool
     */
    public function comparePath(string $path): bool
    {
        $pattern = $this->generateRegexp();
        return preg_match($pattern, $path);
    }

    /**
     * Сравнить текущий роут с запросом.
     * @param Request $request
     * @return bool
     */
    public function compare(Request $request): bool
    {
        $method = $request->getMethod();

        if ($method != $this->method) {
            return false;
        }

        $path = $request->getPath();
        return $this->comparePath($path);
    }

    /**
     * Извлечь параметры из адресной строки.
     * @param Request $request
     * @return array
     */
    public function extractParams(Request $request): array
    {
        $uri = $request->getPath();
        $pattern = $this->generateRegexp();

        if (!preg_match($pattern, $uri, $matches)) {
            return [];
        }

        return array_filter($matches, function ($key) {
            return !is_int($key);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Сгенерировать паттерн для разбора URI.
     * @return string
     */
    protected function generateRegexp(): string
    {
        $pattern = preg_quote($this->path, '/');
        $pattern = preg_replace('/\\\{([a-z]+)\\\}/', '(?P<$1>[^\/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }
}
