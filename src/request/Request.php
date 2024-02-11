<?php

namespace veejay\api\request;

use veejay\api\component\Component;

class Request extends Component
{
    const GET =     'GET';
    const POST =    'POST';
    const PUT =     'PUT';
    const DELETE =  'DELETE';
    const HEAD =    'HEAD';
    const CONNECT = 'CONNECT';
    const OPTIONS = 'OPTIONS';
    const TRACE =   'TRACE';
    const PATCH =   'PATCH';
    const OTHER =   'OTHER'; // На случай, если входной скрипт вызван из консоли или иным методом

    const METHODS = [
        self::GET,
        self::POST,
        self::PUT,
        self::DELETE,
        self::HEAD,
        self::CONNECT,
        self::OPTIONS,
        self::TRACE,
        self::PATCH,
    ];

    /**
     * Вернуть название метода запроса.
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? static::OTHER;
    }

    /**
     * Вернуть полный URI запроса.
     * @return string - /v1/controller?key=value
     */
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }

    /**
     * Вернуть URI запроса без параметров.
     * @return string - {/v1/controller}?key=value
     */
    public function getPath(): string
    {
        $uri = $this->getUri();
        $part = parse_url($uri);
        return $part['path'] ?? '';
    }

    /**
     * Вернуть указанную часть URI запроса, разделенную слешем, без параметров.
     * @param int $index - индекс части, разделенной слешем (/part-1/part-2)
     * @return string|null
     */
    public function getPathPart(int $index): ?string
    {
        $path = $this->getPath();
        $parts = explode('/', $path);
        return $parts[$index] ?? null;
    }

    /**
     * Вернуть массив заголовков.
     * @return array
     */
    public function getHeaders(): array
    {
        return function_exists('getallheaders') ? getallheaders() : [];
    }

    /**
     * Вернуть заголовок с указанным названием.
     * @param string $name
     * @return string|null
     */
    public function getHeader(string $name): ?string
    {
        $headers = $this->getHeaders();
        return $headers[$name] ?? null;
    }

    /**
     * Извлечь токен из заголовка.
     * Authorization: Bearer 50mEt0ken
     * @return string|null
     */
    public function getBearerToken(): ?string
    {
        $authorization = $this->getHeader('Authorization');

        if ($authorization === null) {
            return null;
        }

        if (!preg_match('/^Bearer (.+)$/', $authorization, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Вернуть массив данных, переданных в заголовке запроса.
     * @return array
     */
    public function getHeaderPayload(): array
    {
        $input = $this->getInputStream();

        if ($input === false) {
            return [];
        }

        parse_str($input, $data);
        return $data;
    }

    /**
     * Вернуть массив данных, переданных в адресной строке.
     * @return array
     */
    public function getUriPayload(): array
    {
        return $_GET;
    }

    /**
     * @return bool|string
     */
    protected function getInputStream(): bool|string
    {
        return file_get_contents('php://input');
    }
}
