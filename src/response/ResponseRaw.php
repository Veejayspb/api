<?php

namespace veejay\api\response;

class ResponseRaw extends Response
{
    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        $this->addHeader('Content-Type', 'text/html; charset=utf-8');
        return parent::run();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBody(array $data): string
    {
        $result = print_r($data, true);
        return "<pre>$result</pre>";
    }
}
