<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Request;

final class RequestTest extends TestCase
{
    const BEARER_TOKEN = 'e77989ed21758e78331b20e477fc5582';

    const HEADERS = [
        'host' => 'localhost',
        'x-real-ip' => '127.0.0.1',
        'accept-encoding' => 'gzip, deflate',
        'authorization' => 'Bearer ' . self::BEARER_TOKEN,
        'content-type' => 'text/html;charset=utf-8',
    ];

    public function testGetMethod()
    {
        $request = new Request;

        $actual = $request->getMethod();
        $this->assertSame(Request::OTHER, $actual);

        $_SERVER['REQUEST_METHOD'] = Request::POST;
        $actual = $request->getMethod();
        $this->assertSame(Request::POST, $actual);
    }

    public function testGetScheme()
    {
        $request = new Request;

        $actual = $request->getScheme();
        $this->assertSame('http', $actual);

        $_SERVER['REQUEST_SCHEME'] = 'https';
        $actual = $request->getScheme();
        $this->assertSame('https', $actual);
    }

    public function testGetDomain()
    {
        $request = new Request;

        $actual = $request->getDomain();
        $this->assertSame('', $actual);

        $_SERVER['HTTP_HOST'] = 'domain.ru';
        $actual = $request->getDomain();
        $this->assertSame('domain.ru', $actual);
    }

    public function testGetUri()
    {
        $request = new Request;

        $actual = $request->getUri();
        $this->assertSame('', $actual);

        $_SERVER['REQUEST_URI'] = '/any/path?q=1';
        $actual = $request->getUri();
        $this->assertSame('/any/path?q=1', $actual);
    }

    public function testGetPath()
    {
        $request = new Request;

        $actual = $request->getPath();
        $this->assertSame('', $actual);

        $_SERVER['REQUEST_URI'] = '/any/path?q=1';
        $actual = $request->getPath();
        $this->assertSame('/any/path', $actual);
    }

    public function testGetAbsoluteAddress()
    {
        $_SERVER['REQUEST_SCHEME'] = 'https';
        $_SERVER['HTTP_HOST'] = 'domain.ru';
        $_SERVER['REQUEST_URI'] = '/any/path?q=1';

        $request = new Request;
        $actual = $request->getAbsoluteAddress();
        $this->assertSame('https://domain.ru/any/path', $actual);
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

        $actual = $request->getHeader('host');
        $this->assertSame('localhost', $actual);

        $actual = $request->getHeader('notexists');
        $this->assertNull($actual);
    }

    public function testGetBearerToken()
    {
        $request = $this->getRequest();

        $actual = $request->getBearerToken();
        $this->assertSame(self::BEARER_TOKEN, $actual);
    }

    public function testGetHeaderPayload()
    {
        $request = $this->getRequest();

        $request->headerPayloadRaw = 'a=1&b=2';
        $request->headers = ['content-type' => 'text/html;charset=utf-8'];
        $actual = $request->getHeaderPayload();
        $this->assertSame(['a' => '1', 'b' => '2'], $actual);

        $request->headerPayloadRaw = '{"a":"1","b":"2"}';
        $request->headers = ['content-type' => 'application/json'];
        $actual = $request->getHeaderPayload();
        $this->assertSame(['a' => '1', 'b' => '2'], $actual);
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return new class extends Request
        {
            public ?string $headerPayloadRaw = null;

            public array $headers = RequestTest::HEADERS;

            public function getHeaders(): array
            {
                return $this->headers;
            }

            public function getHeaderPayloadRaw(): ?string
            {
                return $this->headerPayloadRaw;
            }
        };
    }

    protected function setUp(): void
    {
        $items = [
            'REQUEST_METHOD',
            'REQUEST_SCHEME',
            'HTTP_HOST',
            'REQUEST_URI',
        ];

        foreach ($items as $item) {
            if (array_key_exists($item, $_SERVER)) {
                unset($_SERVER[$item]);
            }
        }
    }
}
