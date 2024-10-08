<?php

namespace veejay\test\component;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Создать файл в директории TEMP.
     * @param string $name
     * @param string $content
     * @return bool
     */
    protected function createTempFile(string $name, string $content): bool
    {
        $path = $this->getTempFilePath($name);
        $result = file_put_contents($path, $content, LOCK_EX);
        return $result !== false;
    }

    /**
     * Удалить файл из директории TEMP.
     * @param string $name
     * @return bool
     */
    protected function removeTempFile(string $name): bool
    {
        $path = $this->getTempFilePath($name);

        if (is_file($path)) {
            return unlink($path);
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getTempFilePath(string $name): string
    {
        $dir = sys_get_temp_dir();
        return $dir . DIRECTORY_SEPARATOR . $name;
    }
}
