<?php

namespace veejay\api\response;

class ResponseJson extends Response
{
    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        header('Content-Type: application/json; charset=utf-8');
        return parent::run();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBody(): string
    {
        return json_encode($this->data);
    }
}
