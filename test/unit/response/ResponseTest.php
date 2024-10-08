<?php

use veejay\api\response\Response;
use veejay\test\component\TestCase;

final class ResponseTest extends TestCase
{
    public function testRun()
    {
        $response = $this->getResponse();

        $actual = $response->run();
        $this->assertSame('body', $actual);
        $this->assertSame($response->code, http_response_code());
    }

    /**
     * @return Response
     */
    protected function getResponse(): Response
    {
        return new class extends Response
        {
            public int $code = self::DEFAULT_CODE;

            public array $data = [];

            protected function getBody(array $data): string
            {
                return 'body';
            }
        };
    }
}
