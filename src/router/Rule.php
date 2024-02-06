<?php

namespace veejay\api\router;

use veejay\api\component\Controller;
use veejay\api\request\Request;

class Rule
{
    /**
     * Паттерн адреса.
     * @var string
     */
    protected string $pattern;

    /**
     * Роут.
     * [\path\to\ControllerName::class, 'action', 'METHOD']
     * @var array
     */
    protected array $route;

    /**
     * @param string $pattern
     * @param array $route
     */
    public function __construct(string $pattern, array $route)
    {
        $this->pattern = $pattern;
        $this->route = $route;
    }

    /**
     * Сравнить текущее правило с объектом запроса.
     * @param Request $request
     * @return bool
     */
    public function compare(Request $request): bool
    {
        $method = $this->route[2] ?? null;

        return
            $method === $request->getMethod() &&
            $this->pattern === $request->getUri();
    }

    /**
     * Запустить контроллер/действие и вернуть результат выполнения.
     * @return array|null
     */
    public function run(): ?array
    {
        $controller = $this->getController();

        if (!$controller) {
            return null;
        }

        return $this->runAction($controller);
    }

    /**
     * Инстанцировать контроллер.
     * @return Controller|null
     */
    protected function getController(): ?Controller
    {
        if (isset($this->route[0]) && is_subclass_of($this->route[0], Controller::class)) {
            return new $this->route[0];
        }

        return null;
    }

    /**
     * Вернуть название действия.
     * @return string|null
     */
    protected function getActionName(): ?string
    {
        if (!isset($this->route[1]) || !is_string($this->route[1])) {
            return null;
        }

        return $this->route[1];
    }

    /**
     * Запуск действия.
     * @param Controller $controller
     * @return array|null
     */
    protected function runAction(Controller $controller): ?array
    {
        $action = $this->getActionName();

        if (!method_exists($controller, $action)) {
            return null;
        }

        return call_user_func([$controller, $action]);
    }
}
