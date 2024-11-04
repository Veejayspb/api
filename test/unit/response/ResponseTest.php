<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Code;
use veejay\api\response\Response;

final class ResponseTest extends TestCase
{
    public function testRun()
    {
        $response = $this->getResponse();

        $response->body = [1, 2, 3];
        $body = $response->run();
        $this->assertSame(Code::OK, $response->code);
        $this->assertSame('123', $body);

        $response->body = null;
        $body = $response->run();
        $this->assertSame(Code::NO_CONTENT, $response->code);
        $this->assertSame('', $body);
    }

    public function testAddHeader()
    {
        $response = $this->getResponse();
        $expected = [];

        $response->addHeader('a', 'aa');
        $expected['a'] = 'aa';
        $this->assertSame($expected, $response->head);

        $response->addHeader('b', 'bb');
        $expected['b'] = 'bb';
        $this->assertSame($expected, $response->head);

        $response->addHeader('a', 'aaa');
        $expected['a'] = 'aaa';
        $this->assertSame($expected, $response->head);
    }

    public function testRemoveHeader()
    {
        $response = $this->getResponse();
        $expected = $response->head = [
            'a' => 'aa',
            'b' => 'bb',
        ];

        $response->removeHeader('c');
        $this->assertSame($expected, $response->head);

        $response->removeHeader('b');
        unset($expected['b']);
        $this->assertSame($expected, $response->head);

        $response->removeHeader('a');
        unset($expected['a']);
        $this->assertSame($expected, $response->head);
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
                return implode('', $data);
            }
        };
    }
}
