<?php

namespace veejay\api\component;

abstract class Controller
{
    /**
     * Разрешен ли доступ к действию.
     * @param string $action
     * @return bool
     */
    public function _access(string $action): bool
    {
        return true;
    }
}
