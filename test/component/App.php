<?php

namespace veejay\test\component;

class App extends \veejay\api\App
{
    /**
     * {@inheritdoc}
     */
    protected function data(): string
    {
        return 'data';
    }

    /**
     * {@inheritdoc}
     */
    protected function documentation(): string
    {
        return 'documentation';
    }
}
