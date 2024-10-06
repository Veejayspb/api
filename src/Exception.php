<?php

namespace veejay\api;

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
        $response->data = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }
}
