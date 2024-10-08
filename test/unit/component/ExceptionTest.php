<?php

use veejay\api\component\Exception;
use veejay\api\response\Response;
use veejay\test\component\TestCase;

class ExceptionTest extends TestCase
{
    const CODE = 123;
    const MESSAGE = 'Test message';

    public function testPrepareResponse()
    {
        $response = $this->getResponse();
        $exception = new Exception(self::MESSAGE, self::CODE);
        $exception->prepareResponse($response);

        $this->assertSame(self::CODE, $response->code);
        $this->assertSame(['code' => self::CODE, 'message' => self::MESSAGE], $response->data);
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
