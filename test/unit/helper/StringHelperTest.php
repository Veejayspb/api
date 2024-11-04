<?php

use PHPUnit\Framework\TestCase;
use veejay\api\helper\StringHelper;

final class StringHelperTest extends TestCase
{
    public function testParseStr()
    {
        $str = 'name=qwerty&num=22';
        $actual = StringHelper::parseStr($str);
        $this->assertSame(['name' => 'qwerty', 'num' => '22'], $actual);

        $str = '';
        $actual = StringHelper::parseStr($str);
        $this->assertSame([], $actual);
    }
}
