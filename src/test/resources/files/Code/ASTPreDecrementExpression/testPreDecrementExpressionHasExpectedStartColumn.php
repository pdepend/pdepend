<?php
function testPreDecrementExpressionHasExpectedStartColumn($obj)
{
    return --
        $obj::
            $bar
                ->foo;
}