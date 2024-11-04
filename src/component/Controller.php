<?php

namespace veejay\api\component;

abstract class Controller
{
    /**
     * @param string $name
     * @param array $arguments
     * @return array|null
     * @throws Exception
     */
    public function __call(string $name, array $arguments): ?array
    {
        $methods = get_class_methods($this); // method_exists() - считает приватные методы существующими

        if (!in_array($name, $methods)) {
            throw new Exception('Method not found', 404);
        }

        $result = $this->__access($name, $arguments);

        if (!$result) {
            throw new Exception('Method not allowed', 403);
        }

        return call_user_func_array([$this, $name], $arguments);
    }

    /**
     * Проверка на наличие доступа к действию.
     * При отсутствии доступа возваращать FALSE, либо выбрасывать исключение внутри данного метода.
     * @param string $action
     * @param array $arguments
     * @return bool
     */
    protected function __access(string $action, array $arguments): bool
    {
        return true;
    }
}
