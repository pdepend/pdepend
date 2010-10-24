<?php
function testEvalExpressionHasExpectedStartLine()
{
    echo eval(
        "return
            'echo';"
                )
    . "foo";
}

testEvalExpressionHasExpectedStartLine();