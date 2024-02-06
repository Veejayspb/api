<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\Controller;

final class ControllerTest extends TestCase
{
    public function testAccess()
    {
        $object = $this->getController();
        $actions = [
            'index',
            'view',
            'test',
            '',
            ' ',
        ];

        foreach ($actions as $action) {
            $actual = $object->_access($action);
            $this->assertTrue($actual);
        }
    }

    /**
     * @return Controller
     */
    protected function getController(): Controller
    {
        return new class extends Controller {};
    }
}
