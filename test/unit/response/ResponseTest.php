<?php

use PHPUnit\Framework\TestCase;
use veejay\api\response\Response;

final class ResponseTest extends TestCase
{
    const BODY = 'body';
    const CODE = 123;

    public function testRun()
    {
        $response = $this->getResponse();
        $response->code = self::CODE;

        $this->assertSame(self::BODY, $response->run());
        $this->assertSame(self::CODE, http_response_code());
    }

    /**
     * @return Response
     */
    protected function getResponse(): Response
    {
        return new class extends Response
        {
            protected function getBody(): string
            {
                return ResponseTest::BODY;
            }
        };
    }
}
