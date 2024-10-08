<?php

use veejay\api\component\View;
use veejay\test\component\TestCase;

class ViewTest extends TestCase
{
    const TEMPLATE = '<?php /* @var string $content */ echo $content;';
    const CSS = 'body {color:red;}';
    const JS = 'alert("success");';
    const CONTENT = 'text';

    public function testRender()
    {
        $name = 'template.php';

        $this->createTempFile($name, self::TEMPLATE);
        $path = $this->getTempFilePath($name);
        $view = new View;
        $actual = $view->render($path, ['content' => self::CONTENT]);
        $this->removeTempFile($name);

        $this->assertSame(self::CONTENT, $actual);
    }

    public function testRenderCss()
    {
        $name = 'style.css';

        $this->createTempFile($name, self::CSS);
        $path = $this->getTempFilePath($name);
        $view = new View;
        $actual = $view->renderCss($path);
        $this->removeTempFile($name);

        $this->assertSame('<style>' . self::CSS . '</style>', $actual);
    }

    public function testRenderJs()
    {
        $name = 'script.js';

        $this->createTempFile($name, self::JS);
        $path = $this->getTempFilePath($name);
        $view = new View;
        $actual = $view->renderJs($path);

        $this->assertSame('<script type="text/javascript">' . self::JS . '</script>', $actual);
    }
}
