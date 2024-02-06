<?php

use PHPUnit\Framework\TestCase;
use veejay\api\response\ResponseXml;

final class ResponseXmlTest extends TestCase
{
    const EXPECTED = "<?xml version=\"1.0\"?>\n<response><item>1</item><a/><b><item>1</item><item>2</item></b><item/>< >1</ ></response>\n";

    public function testRun()
    {
        $response = new ResponseXml;
        $response->data = [
            1,
            'a' => null,
            'b' => [1, 2],
            '' => false,
            ' ' => true,
        ];

        $this->assertSame(self::EXPECTED, $response->run());
    }
}
