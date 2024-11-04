<?php

use PHPUnit\Framework\TestCase;
use veejay\api\response\Response;

final class ExceptionTest extends TestCase
{
    const CODE = 500;
    const MESSAGE = 'test message';

    public function testPrepareResponse()
    {
        $response = $this->getResponse();

        $exception = new \veejay\api\component\Exception(self::MESSAGE, self::CODE);
        $exception->prepareResponse($response);

        $this->assertSame(self::CODE, $response->code);
        $this->assertSame(['code' => self::CODE, 'message' => self::MESSAGE], $response->body);
    }

    /**
     * @return Response
     */
    protected function getResponse(): Response
    {
        return new class extends Response
        {
            protected function getBody(array $data): string
            {
                return '';
            }
        };
    }
}
