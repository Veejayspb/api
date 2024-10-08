<?php

use veejay\api\response\ResponseRaw;
use veejay\test\component\TestCase;

final class ResponseRawTest extends TestCase
{
    public function testRun()
    {
        $response = new ResponseRaw;
        $response->data = ['content'];

        $actual = $response->run();
        $this->assertSame('<pre>' . print_r(['content'], true) . '</pre>', $actual);
        $this->assertSame($response->code, http_response_code());
    }
}
