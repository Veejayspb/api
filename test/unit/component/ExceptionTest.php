<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Exception;
use veejay\api\response\Response;

final class ExceptionTest extends TestCase
{
    public function testPrepareResponse()
    {
        $code = 123;
        $message = 'msg';

        $response = $this->getResponse();
        $exception = new Exception($message, $code);
        $exception->prepareResponse($response);

        $this->assertSame(123, $response->code);
        $this->assertSame(
            ['code' => $code, 'message' => $message],
            $response->data
        );
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
                return '';
            }
        };
    }
}
