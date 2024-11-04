<?php

namespace veejay\api\component;

use veejay\api\response\Response;

class Exception extends \Exception
{
    /**
     * Заполнить объект с ответом.
     * @param Response $response
     * @return void
     */
    public function prepareResponse(Response $response): void
    {
        $response->code = $this->getCode();
        $response->body = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }
}
