<?php

use PHPUnit\Framework\TestCase;
use veejay\api\response\ResponseJson;

final class ResponseJsonTest extends TestCase
{
    const EXPECTED = '{"0":1,"a":null,"b":[1,2],"":false," ":true}';

    public function testRun()
    {
        $response = new ResponseJson;
        $response->data = [
            1,
            'a' => null,
            'b' => [1, 2],
            '' => false,
            ' ' => true,
        ];

        $this->assertSame(self::EXPECTED, $response->run());
    }
}
