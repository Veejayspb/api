<?php

namespace veejay\api\router;

use veejay\api\request\Request;

interface RuleInterface
{
    /**
     * Сравнить текущее правило с объектом запроса.
     * @param Request $request
     * @return bool
     */
    public function compare(Request $request): bool;

    /**
     * Извлечь параметры из пути.
     * @param string $path
     * @return array
     */
    public function getParams(string $path): array;

    /**
     * Запустить контроллер/действие и вернуть результат выполнения.
     * @param array $params
     * @return array
     */
    public function run(array $params): array;
}
