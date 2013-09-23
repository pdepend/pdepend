<?php
class testParserHandlesRegularArraySyntaxClass
{
    function testParserHandlesRegularArraySyntax($x)
    {
       return array(
           1 => 2,
           2 => array(
               new stdClass(),
               'foo' => &$x,
               'bar' => $x,
               array(
                   'a', 'b', 'c', new stdClass()
               )
           )
       );
    }
}
