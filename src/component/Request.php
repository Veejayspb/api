<?php

namespace veejay\api\component;

use veejay\api\helper\StringHelper;

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
     * Вернуть схему.
     * @return string - {https}://domain.ru/v1/users?key=value
     */
    public function getScheme(): string
    {
        return $_SERVER['REQUEST_SCHEME'] ?? 'http';
    }

    /**
     * Вернуть адрес домена.
     * @return string - https://{domain.ru}/v1/users?key=value
     */
    public function getDomain(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
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
     * Вернуть абсолютный адрес запроса.
     * @return string - {https://domain.ru/v1/users}?key=value
     */
    public function getAbsoluteAddress(): string
    {
        return
            $this->getScheme() . '://' .
            $this->getDomain() .
            $this->getPath();
    }

    /**
     * Вернуть массив заголовков.
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        // Заголовки регистронезависимые, поэтому все приведены к нижнему регистру
        foreach ($headers as $name => $value) {
            unset($headers[$name]);
            $name = strtolower($name);
            $headers[$name] = $value;
        }

        return $headers;
    }

    /**
     * Вернуть заголовок с указанным названием.
     * @param string $name
     * @return string|null
     */
    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        $headers = $this->getHeaders();
        return $headers[$name] ?? null;
    }

    /**
     * Извлечь токен из заголовка.
     * Authorization: Bearer sometoken
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
     * Вернуть исходную строку с данными, переданными в заголовке запроса.
     * @return string|null
     */
    public function getHeaderPayloadRaw(): ?string
    {
        $content = file_get_contents('php://input');
        return $content === false ? null : $content;
    }

    /**
     * Вернуть декодированный массив данных, переданных в заголовке запроса.
     * @return array
     */
    public function getHeaderPayload(): array
    {
        $payload = $this->getHeaderPayloadRaw();

        if ($payload === null) {
            return [];
        }

        $contentType = $this->getHeader('content-type');

        return match ($contentType) {
            'application/json' => json_decode($payload, true) ?? [],
            default => StringHelper::parseStr($payload),
        };
    }
}
