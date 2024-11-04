<?php

namespace veejay\api\response;

use veejay\api\component\Code;

abstract class Response
{
    /**
     * Код ответа.
     * @var int
     */
    public int $code = Code::OK;

    /**
     * Тело ответа.
     * @var array|null
     */
    public ?array $body;

    /**
     * Заголовки ответа.
     * @var array
     */
    public array $head = [];

    /**
     * Сгенерировать готовый результат для вывода в качестве ответа.
     * @return string
     */
    public function run(): string
    {
        if ($this->body === null) {
            $this->code = Code::NO_CONTENT;
        }

        http_response_code($this->code);
        $this->registerHeaders();
        return $this->body === null ? '' : $this->getBody($this->body);
    }

    /**
     * Добавить заголовок.
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addHeader(string $name, string $value): void
    {
        $name = strtolower($name);
        $this->head[$name] = $value;
    }

    /**
     * Удалить заголовок.
     * @param string $name
     * @return void
     */
    public function removeHeader(string $name): void
    {
        $name = strtolower($name);

        if (array_key_exists($name, $this->head)) {
            unset($this->head[$name]);
        }
    }

    /**
     * Регистрация заголовков.
     * @return void
     */
    protected function registerHeaders(): void
    {
        foreach ($this->head as $name => $value) {
            header($name . ': ' . $value);
        }
    }

    /**
     * Формирование тела ответа.
     * @param array $data - данные, передаваемые в ответ
     * @return string
     */
    abstract protected function getBody(array $data): string;
}
