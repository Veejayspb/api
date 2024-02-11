<?php

namespace veejay\api\response;

use veejay\api\component\Component;

abstract class Response extends Component
{
    const DEFAULT_CODE = 200;

    /**
     * Код ответа.
     * @var int
     */
    public int $code = self::DEFAULT_CODE;

    /**
     * Данные для ответа.
     * При успехе вернутся данные.
     * При ошибке вернутся подробности ошибки.
     * @var array
     */
    public array $data = [];

    /**
     * Сгенерировать готовый результат для вывода в качестве ответа.
     * @return string
     */
    public function run(): string
    {
        http_response_code($this->code);
        return $this->getBody();
    }

    /**
     * Формирование тела ответа.
     * @return string
     */
    abstract protected function getBody(): string;
}
