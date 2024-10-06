<?php

namespace veejay\api\response;

class ResponseRaw extends Response
{
    /**
     * {@inheritdoc}
     */
    protected function getBody(array $data): string
    {
        $result = print_r($data, true);
        return "<pre>$result</pre>";
    }
}
