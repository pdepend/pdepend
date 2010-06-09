<?php
function testPreDecrementExpressionHasExpectedEndColumn($obj)
{
    return --
        $obj::
            $bar
                ->foo;
}