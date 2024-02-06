<?php

namespace veejay\api\router;

use veejay\api\request\Request;

class Router
{
    /**
     * Объекта запроса.
     * @var Request
     */
    protected Request $request;

    /**
     * Правила роутинга.
     * @var array
     */
    protected array $rules;

    /**
     * @param Request $request
     * @param array $rules
     */
    public function __construct(Request $request, array $rules)
    {
        $this->request = $request;
        $this->rules = $rules;
    }

    /**
     * Вернуть подходящее запросу правило.
     * @return Rule|null
     */
    public function getRule(): ?Rule
    {
        foreach ($this->rules as $pattern => $route) {
            $rule = $this->createRule($pattern, $route);

            if ($rule->compare($this->request)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Инстанцировать объект с правилом.
     * @param string $pattern
     * @param array $route
     * @return Rule
     */
    protected function createRule(string $pattern, array $route): Rule
    {
        return new Rule($pattern, $route);
    }
}
