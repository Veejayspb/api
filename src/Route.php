<?php

namespace veejay\api;

class Route
{
    /**
     * Описание.
     * @var string
     */
    public string $description = '';

    /**
     * Паттерн URI.
     * @var string
     */
    public string $uri;

    /**
     * Название метода.
     * @var string
     */
    public string $method;

    /**
     * Коллбэк с ответом.
     * @var callable
     */
    public $callback;

    /**
     * Список передаваемых параметров.
     * @var array
     */
    public array $params = [];

    /**
     * Список обязательных параметров.
     * @var array
     */
    public array $required = [];

    /**
     * Примеры данных, которые вернутся в ответе.
     * Ключ - код ответа.
     * Значение - пример массива данных, который вернется.
     * @var array
     */
    public array $returns = [];

    /**
     * Объект приложения.
     * @var Application
     */
    protected Application $application;

    /**
     * @param Application $application
     * @param array $properties
     */
    public function __construct(Application $application, array $properties)
    {
        $this->application = $application;

        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Вернуть паттерн URI, включая базовый путь.
     * @return string
     */
    public function getUri(): string
    {
        return $this->application->baseUri . $this->uri;
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

        $uri = $request->getPath();
        $pattern = $this->generateRegexp();

        return preg_match($pattern, $uri) === 1;
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
     * Запуск коллбэка и возвращение результата.
     * @return array
     */
    public function runCallback(): array
    {
        return call_user_func($this->callback);
    }

    /**
     * Вернуть список обязательных параметров.
     * Отличается от прямого обращения к $this->required тем, что проверяет в списке параметров их наличие.
     * @return array
     */
    public function getRequired(): array
    {
        return array_intersect(
            array_keys($this->params),
            $this->required
        );
    }

    /**
     * Все ли обязательные параметры заполнены в переданном массиве.
     * @param array $params
     * @return bool
     */
    public function checkRequired(array $params): bool
    {
        $required = $this->getRequired();
        $keys = array_keys($params);

        return !array_diff($required, $keys);
    }

    /**
     * Является ли параметр обязательным.
     * @param string $param
     * @return bool
     */
    public function isRequired(string $param): bool
    {
        return in_array($param, $this->required);
    }

    /**
     * Сгенерировать паттерн для разбора URI.
     * @return string
     */
    private function generateRegexp(): string
    {
        $uri = $this->getUri();
        $pattern = preg_quote($uri, '/');
        $pattern = preg_replace('/\\\{([a-z]+)\\\}/', '(?P<$1>[0-9]+)', $pattern);
        return '/^' . $pattern . '$/';
    }
}
