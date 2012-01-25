<?php
class testParserHandlesShortArraySyntaxClass
{
    function testParserHandlesShortArraySyntax()
    {
        return [
            1 => 2,
            2 => [
                new stdClass(),
                'foo' => &$x,
                'bar' => $x,
                [
                    'a', 'b', 'c', new stdClass()
                ]
            ]
        ];
    }
}
