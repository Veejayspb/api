<?php

use veejay\api\component\Request;
use veejay\test\component\TestCase;

class RequestTest extends TestCase
{
    const HEADERS = [
        'Host' => 'localhost',
        'X-Real-IP' => '127.0.0.1',
        'Accept-Encoding' => 'gzip, deflate',
        'Authorization' => 'Bearer e77989ed21758e78331b20e477fc5582',
    ];

    public function testGetMethod()
    {
        $request = new Request;
        $actual = $request->getMethod();
        $this->assertSame(Request::OTHER, $actual);

        $_SERVER['REQUEST_METHOD'] = Request::POST;
        $actual = $request->getMethod();
        $this->assertSame(Request::POST, $actual);

        $_SERVER['REQUEST_METHOD'] = 'UNDEFINED';
        $actual = $request->getMethod();
        $this->assertSame('UNDEFINED', $actual);
    }

    public function testGetUri()
    {
        $request = new Request;

        $_SERVER['REQUEST_URI'] = $expected = '/';
        $actual = $request->getUri();
        $this->assertSame($expected, $actual);

        $_SERVER['REQUEST_URI'] = $expected = '/some/uri';
        $actual = $request->getUri();
        $this->assertSame($expected, $actual);
    }

    public function testGetPath()
    {
        $request = new Request;

        $_SERVER['REQUEST_URI'] = '';
        $actual = $request->getPath();
        $this->assertSame('', $actual);

        $_SERVER['REQUEST_URI'] = '/some/path?key=value';
        $actual = $request->getPath();
        $this->assertSame('/some/path', $actual);

        $_SERVER['REQUEST_URI'] = '?key=value';
        $actual = $request->getPath();
        $this->assertSame('', $actual);
    }

    public function testGetHeaders()
    {
        if (!function_exists('getallheaders')) {
            $request = new Request;
            $actual = $request->getHeaders();
            $this->assertSame([], $actual);

            function getallheaders() {
                return RequestTest::HEADERS;
            }

            $actual = $request->getHeaders();
            $this->assertSame(self::HEADERS, $actual);
        }
    }

    public function testGetHeader()
    {
        $request = $this->getRequest();

        $actual = $request->getHeader('Host');
        $this->assertSame('localhost', $actual);

        $actual = $request->getHeader('notexists');
        $this->assertNull($actual);
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return new class extends Request
        {
            public function getHeaders(): array
            {
                return RequestTest::HEADERS;
            }
        };
    }
}
