<?php

namespace veejay\api\component;

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
            throw new Exception("View not found: $path", Code::INTERNAL_SERVER_ERROR);
        }

        extract($data);

        ob_start();
        include $path;
        return ob_get_clean();
    }
}