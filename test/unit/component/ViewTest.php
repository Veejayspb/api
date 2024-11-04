<?php

use PHPUnit\Framework\TestCase;
use veejay\api\component\View;

final class ViewTest extends TestCase
{
    const CONTENT = 'any content';

    public function testRender()
    {
        $path = dirname(__DIR__) . '/template/index.php';
        $view = new View;
        $actual = $view->render($path, ['content' => self::CONTENT]);
        $this->assertSame(self::CONTENT, $actual);
    }
}
