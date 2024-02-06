<?php

use PHPUnit\Framework\TestCase;
use veejay\api\request\Request;

final class RequestTest extends TestCase
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
        $actual = $request->getUri();
        $this->assertSame('', $actual);

        $_SERVER['REQUEST_URI'] = $expected = '/some/uri';
        $actual = $request->getUri();
        $this->assertSame($expected, $actual);
    }

    public function testGetPath()
    {
        $request = new Request;
        $actual = $request->getPath();
        $this->assertSame('', $actual);

        $_SERVER['REQUEST_URI'] = '/some/path?key=value';
        $actual = $request->getPath();
        $this->assertSame('/some/path', $actual);

        $_SERVER['REQUEST_URI'] = '?key=value';
        $actual = $request->getPath();
        $this->assertSame('', $actual);
    }

    public function testGetPathPart()
    {
        $request = new Request;
        $_SERVER['REQUEST_URI'] = '/v1/user/1?name=value';

        $actual = $request->getPathPart(-1);
        $this->assertNull($actual);

        $actual = $request->getPathPart(0);
        $this->assertSame('', $actual);

        $actual = $request->getPathPart(1);
        $this->assertSame('v1', $actual);

        $actual = $request->getPathPart(2);
        $this->assertSame('user', $actual);

        $actual = $request->getPathPart(3);
        $this->assertSame('1', $actual);

        $actual = $request->getPathPart(4);
        $this->assertNull($actual);
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

    public function testGetBearerToken()
    {
        $request = $this->getRequest();

        $actual = $request->getBearerToken();
        $this->assertSame('e77989ed21758e78331b20e477fc5582', $actual);

        $request->headers = [];
        $actual = $request->getBearerToken();
        $this->assertNull($actual);
    }

    public function testGetHeaderPayload()
    {
        $request = $this->getRequest();
        $request->inputStream = false;
        $actual = $request->getHeaderPayload();
        $this->assertSame([], $actual);

        $request->inputStream = '';
        $actual = $request->getHeaderPayload();
        $this->assertSame([], $actual);

        $request->inputStream = 'a=1&b=&c&d[]=4&d[]=5';
        $actual = $request->getHeaderPayload();
        $this->assertSame(['a' => '1', 'b' => '', 'c' => '', 'd' => ['4', '5']], $actual);
    }

    public function testGetUriPayload()
    {
        $request = new Request;
        $actual = $request->getUriPayload();
        $this->assertSame([], $actual);

        $_GET['key'] = 'value';
        $actual = $request->getUriPayload();
        $this->assertSame($_GET, $actual);
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return new class extends Request
        {
            public array $headers = RequestTest::HEADERS;
            public string|bool $inputStream = false;

            public function getHeaders(): array
            {
                return $this->headers;
            }

            protected function getInputStream(): bool|string
            {
                return $this->inputStream;
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = null;
        $_SERVER['REQUEST_URI'] = null;
        $_GET = [];
    }
}
