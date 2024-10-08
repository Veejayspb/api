<?php

use veejay\api\response\ResponseJson;
use veejay\test\component\TestCase;

final class ResponseJsonTest extends TestCase
{
    public function testRun()
    {
        $response = new ResponseJson;
        $response->data = ['content'];

        $actual = $response->run();
        $this->assertSame('["content"]', $actual);
        $this->assertSame($response->code, http_response_code());
    }
}
