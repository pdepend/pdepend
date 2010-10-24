<?php
function testPreDecrementExpressionHasExpectedEndLine($obj)
{
    return --
        $obj::
            $bar
                ->foo;
}