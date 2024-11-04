<?php

use PHPUnit\Framework\TestCase;
use veejay\api\response\ResponseRaw;

final class ResponseRawTest extends TestCase
{
    const HEAD = [
        'content-type' => 'text/html; charset=utf-8',
    ];

    public function testRun()
    {
        $response = new ResponseRaw;

        $response->body = ['a' => 'aa'];
        $body = $response->run();
        $this->assertSame(self::HEAD, $response->head);
        $this->assertSame('<pre>Array([a]=>aa)</pre>', str_replace(["\n", ' '], '', $body));

        $response->body = null;
        $body = $response->run();
        $this->assertSame(self::HEAD, $response->head);
        $this->assertSame('', $body);
    }
}
