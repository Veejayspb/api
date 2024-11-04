<?php

namespace veejay\api\example;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0', description: 'Описание тестового API.', title: 'Тестовый API')]
#[OA\Server(url: 'http://domain.ru', description: 'production')]
class App extends \veejay\api\App
{
    public string $name = 'Тестовый API';
}
