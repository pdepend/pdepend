<?php
function testDecrementPostfixExpressionHasExpectedEndLine()
{
    for (
        $i = 0;
            $i < 9;
                $i
        // Comment
        --
    ) {}
}