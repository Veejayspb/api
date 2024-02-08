<?php

namespace veejay\api\router;

use veejay\api\request\Request;

class Router
{
    /**
     * Объекта запроса.
     * @var Request
     */
    public Request $request;

    /**
     * Правила роутинга.
     * @var array
     */
    public array $rules;

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
     * @return RuleInterface|null
     */
    public function findRule(): ?RuleInterface
    {
        foreach ($this->rules as $properties) {
            $rule = $this->createRule($properties);
            $compare = $rule->compare($this->request);

            if ($compare) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Инстанцировать объект с правилом.
     * @param array $properties
     * @return RuleInterface
     */
    protected function createRule(array $properties): RuleInterface
    {
        $pattern = $properties[0] ?? '';
        $controller = $properties[1] ?? '';
        $action = $properties[2] ?? '';

        if (array_key_exists(3, $properties)) {
            return new Rule($pattern, $controller, $action, $properties[3]);
        } else {
            return new Rule($pattern, $controller, $action);
        }
    }
}
