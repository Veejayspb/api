<?php

namespace veejay\api\test\component;

final class App extends \veejay\api\App
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();

        $_SERVER['REQUEST_SCHEME'] = 'https';
        $_SERVER['HTTP_HOST'] = 'domain.local';
        $_SERVER['REQUEST_URI'] = '/any/path';
    }
}
