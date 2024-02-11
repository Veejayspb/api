<?php

namespace unit\component;

use PHPUnit\Framework\TestCase;
use veejay\api\component\Component;

class ComponentTest extends TestCase
{
    const VALUE_EMPTY = '';
    const VALUE_FILLED = 'value';

    const PROPERTIES = [
        'a' => self::VALUE_FILLED,
        'b' => self::VALUE_FILLED,
        'c' => self::VALUE_FILLED,
        'd' => self::VALUE_FILLED,
    ];

    public function testConstruct()
    {
        $component = $this->getComponent(self::PROPERTIES);
        $this->checkProperties($component);
    }

    public function testSetProperties()
    {
        $component = $this->getComponent();
        $component->setProperties(self::PROPERTIES);
        $this->checkProperties($component);
    }

    /**
     * @param Component $component
     * @return void
     */
    protected function checkProperties($component): void
    {
        $this->assertSame(self::VALUE_FILLED, $component->a);
        $this->assertSame(self::VALUE_EMPTY, $component->getB());
        $this->assertSame(self::VALUE_EMPTY, $component->getC());
        $this->assertSame(self::VALUE_EMPTY, $component::$d);
    }

    /**
     * @param array $properties
     * @return Component
     */
    protected function getComponent(array $properties = [])
    {
        return new class ($properties) extends Component
        {
            public string $a = ComponentTest::VALUE_EMPTY;
            protected string $b = ComponentTest::VALUE_EMPTY;
            private string $c = ComponentTest::VALUE_EMPTY;
            public static string $d = ComponentTest::VALUE_EMPTY;

            public function getB(): string
            {
                return $this->b;
            }

            public function getC(): string
            {
                return $this->c;
            }
        };
    }
}
