<?php
function testParserHandlesNestedCastExpressions($param)
{
    return (array)
        (int)
            (bool)
                (object)
                    $param;
}
var_dump(testParserHandlesNestedCastExpressions(array('foo' => null)));