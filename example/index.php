<?php

use veejay\api\component\Request;
use veejay\api\example\App;
use veejay\api\example\controller\UserController;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$items = [
    [
        'path' => '/',
        'method' => Request::GET,
        'callback' => function () {
            $isYaml = (bool)($_GET['yaml'] ?? false);
            $app = App::instance();

            if ($isYaml) {
                $path = dirname(__DIR__) . '/example';
                echo $app->swaggerYaml($path);
            } else {
                $url = $app->request->getAbsoluteAddress() . '?' . http_build_query(['yaml' => 1]);
                echo $app->swaggerHtml($url);
            }
            die;
        },
    ],
    [
        'path' => '/user',
        'method' => Request::GET,
        'callback' => [new UserController, 'index'],
    ],
    [
        'path' => '/user/{id}',
        'method' => Request::GET,
        'callback' => [new UserController, 'view'],
    ],
    [
        'path' => '/user',
        'method' => Request::POST,
        'callback' => [new UserController, 'create'],
    ],
    [
        'path' => '/user/{id}',
        'method' => Request::PATCH,
        'callback' => [new UserController, 'update'],
    ],
    [
        'path' => '/user/{id}',
        'method' => Request::DELETE,
        'callback' => [new UserController, 'delete'],
    ],
];

echo App::instance()
    ->addRoutes($items)
    ->run();
