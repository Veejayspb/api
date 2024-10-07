<?php

namespace veejay\api\component;

class Request
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
    const OTHER =   'OTHER'; // Если входной скрипт вызван из консоли или иным методом

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
     * @return string - /v1/users?key=value
     */
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }

    /**
     * Вернуть URI запроса без параметров.
     * @return string - {/v1/users}?key=value
     */
    public function getPath(): string
    {
        $uri = $this->getUri();
        $part = parse_url($uri);
        return $part['path'] ?? '';
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
     * Вернуть массив данных, переданных в заголовке запроса.
     * @return array
     */
    public function getHeaderPayload(): array
    {
        $content = file_get_contents('php://input');

        if ($content === false) {
            return [];
        }

        parse_str($content, $data);
        return $data;
    }
}
