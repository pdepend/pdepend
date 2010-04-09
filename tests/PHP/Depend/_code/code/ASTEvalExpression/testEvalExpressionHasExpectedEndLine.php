<?php
function testEvalExpressionHasExpectedEndLine()
{
    echo eval(
        "return
            'echo';"
                )
    . "foo";
}

testEvalExpressionHasExpectedEndLine();