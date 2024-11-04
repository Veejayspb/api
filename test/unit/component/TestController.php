<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Controller;

final class TestController extends TestCase
{
    const RESULT = ['success'];

    public function testCallSuccess()
    {
        $controller = $this->getController();

        $controller->access = true;
        $result = call_user_func([$controller, 'test']);
        $this->assertSame(self::RESULT, $result);
    }

    public function testCallForbidden()
    {
        $controller = $this->getController();

        $controller->access = false;
        $this->expectExceptionCode(403);
        call_user_func([$controller, 'test']);
    }

    public function testCallNotFound()
    {
        $controller = $this->getController();

        $this->expectExceptionCode(404);
        call_user_func([$controller, 'unexisting_method']);
    }

    /**
     * @return Controller
     */
    protected function getController()
    {
        return new class extends Controller
        {
            public bool $access = true;

            protected function __access(string $action, array $arguments): bool
            {
                return $this->access;
            }

            protected function test()
            {
                return TestController::RESULT;
            }
        };
    }
}
