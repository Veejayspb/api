<?php

use PHPUnit\Framework\TestCase;
use veejay\api\response\ResponseJson;

final class ResponseJsonTest extends TestCase
{
    const HEAD = [
        'content-type' => 'application/json; charset=utf-8',
    ];

    public function testRun()
    {
        $response = new ResponseJson;

        $response->body = ['a' => 'aa'];
        $body = $response->run();
        $this->assertSame(self::HEAD, $response->head);
        $this->assertSame('{"a":"aa"}', $body);

        $response->body = null;
        $body = $response->run();
        $this->assertSame(self::HEAD, $response->head);
        $this->assertSame('', $body);
    }
}
