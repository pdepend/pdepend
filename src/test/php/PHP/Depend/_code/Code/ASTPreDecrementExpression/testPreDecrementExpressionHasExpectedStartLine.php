<?php
function testPreDecrementExpressionHasExpectedStartLine($obj)
{
    return --
        $obj::
            $bar
                ->foo;
}