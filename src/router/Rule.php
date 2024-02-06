<?php

namespace veejay\api\router;

use veejay\api\component\Controller;
use veejay\api\component\Exception;
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
            $this->pattern === $request->getPath();
    }

    /**
     * Запустить контроллер/действие и вернуть результат выполнения.
     * @return array
     * @throws Exception
     */
    public function run(): array
    {
        $controller = $this->getController();
        $actionName = $this->getActionName();

        if ($actionName === null) {
            throw new Exception('Action name not specified', 500);
        }

        if (!method_exists($controller, $actionName)) {
            throw new Exception('Method not found', 404);
        }

        if (!$controller->_access($actionName)) {
            throw new Exception('Access forbidden', 403);
        }

        return call_user_func([$controller, $actionName]);
    }

    /**
     * Вернуть название контроллера.
     * @return string|null
     */
    public function getControllerName(): ?string
    {
        if (!isset($this->route[0]) || !is_string($this->route[0])) {
            return null;
        }

        return $this->route[0];
    }

    /**
     * Вернуть название действия.
     * @return string|null
     */
    public function getActionName(): ?string
    {
        if (!isset($this->route[1]) || !is_string($this->route[1])) {
            return null;
        }

        return $this->route[1];
    }

    /**
     * Инстанцировать контроллер.
     * @return Controller
     * @throws Exception
     */
    protected function getController(): Controller
    {
        $controllerName = $this->getControllerName();

        if ($controllerName === null) {
            throw new Exception('Controller name not specified', 500);
        }

        if (!is_subclass_of($controllerName, Controller::class)) {
            throw new Exception('Invalid controller name', 500);
        }

        return new $controllerName;
    }
}
