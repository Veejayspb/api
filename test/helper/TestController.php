<?php

namespace test\helper;

use veejay\api\component\Controller;

final class TestController extends Controller
{
    public function _access(string $action): bool
    {
        return $action != 'forbidden';
    }

    public function index(?string $name = null): array
    {
        return ['index', $name];
    }

    public function forbidden(): array
    {
        return ['forbidden'];
    }
}
