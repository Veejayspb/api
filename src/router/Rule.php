<?php

namespace veejay\api\router;

use veejay\api\component\Controller;
use veejay\api\component\Exception;
use veejay\api\request\Request;

class Rule implements RuleInterface
{
    const DEFAULT_METHOD = Request::GET;
    const DEFAULT_PARAM_PATTERN = '[^\/]+';

    /**
     * Паттерн адреса.
     * Параметры могут быть произвольными {param} или с шаблоном {param:[a-z]+}.
     * @var string - /some/pattern/{id:[0-9]+}/{prefix}_{name:[a-z]+}
     */
    public string $pattern;

    /**
     * Название класса контроллера.
     * @var string
     */
    public string $controller;

    /**
     * Название действия.
     * @var string
     */
    public string $action;

    /**
     * Название метода.
     * @var string
     */
    public string $method;

    /**
     * @param string $pattern
     * @param string $controller
     * @param string $action
     * @param string $method
     */
    public function __construct(
        string $pattern,
        string $controller,
        string $action,
        string $method = self::DEFAULT_METHOD,
    ) {
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->action = $action;
        $this->method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function compare(Request $request): bool
    {
        $path = $request->getPath();

        return
            $this->method == $request->getMethod() &&
            $this->comparePath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getParams(string $path): array
    {
        $pattern = $this->getPattern();
        preg_match($pattern, $path, $matches);

        return array_filter($matches, function ($key) {
            return is_string($key);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function run(array $params): array
    {
        if (!is_subclass_of($this->controller, Controller::class)) {
            throw new Exception('Invalid controller name', 500);
        }

        $controller = new $this->controller; /* @var Controller $controller */

        if (!method_exists($controller, $this->action)) {
            throw new Exception('Method not found', 404);
        }

        if (!$controller->_access($this->action)) {
            throw new Exception('Access forbidden', 403);
        }

        return call_user_func_array([$controller, $this->action], $params);
    }

    /**
     * Сравнить указанный путь с текущим правилом.
     * @param string $path
     * @return bool
     */
    private function comparePath(string $path): bool
    {
        $pattern = $this->getPattern();
        return preg_match($pattern, $path);
    }

    /**
     * Сформировать паттерн для разбора адреса регулярным выражением.
     * @return string
     */
    private function getPattern(): string
    {
        $result = str_replace('/', '\/', $this->pattern);
        preg_match_all('/{.+?}/', $result, $matches);

        foreach ($matches[0] as $substr) {
            $placeholder = $this->preparePlaceholder($substr);
            $result = str_replace($substr, $placeholder, $result);
        }

        return '/^' . $result . '$/';
    }

    /**
     * Заменить плейсхолдер на регулярное выражение.
     * @param string $placeholder
     * @return string
     */
    private function preparePlaceholder(string $placeholder): string
    {
        $placeholder = trim($placeholder, '{}'); // {name:pattern} => name:pattern
        $parts = explode(':', $placeholder);
        $name = $parts[0];
        $pattern = $parts[1] ?? static::DEFAULT_PARAM_PATTERN;
        return "(?P<$name>$pattern)";
    }
}
