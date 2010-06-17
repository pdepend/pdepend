<?php
function testDecrementPostfixExpressionHasExpectedEndColumn()
{
    for (
        $i = 0;
            $i < 9;
                $i
        // Comment
        --
    ) {}
}