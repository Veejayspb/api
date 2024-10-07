<?php

namespace veejay\api\component;

use veejay\api\component\Exception;

class View
{
    /**
     * Рендер вида.
     * @param string $path - путь до файла
     * @param array $data - данные, передаваемые в файл
     * @return string
     * @throws Exception
     */
    public function render(string $path, array $data = []): string
    {
        if (!is_file($path)) {
            throw new Exception("View not found: $path", 500);
        }

        extract($data);

        ob_start();
        include $path;
        return ob_get_clean();
    }

    /**
     * Рендер тега со стилями.
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function renderCss(string $path): string
    {
        $content = $this->render($path);
        return '<style>' . $content . '</style>';
    }

    /**
     * Рендер тега со скриптами.
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function renderJs(string $path): string
    {
        $content = $this->render($path);
        return '<script type="text/javascript">' . $content . '</script>';
    }
}
