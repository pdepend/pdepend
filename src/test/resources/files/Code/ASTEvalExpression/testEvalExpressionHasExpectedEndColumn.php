<?php
function testEvalExpressionHasExpectedEndColumn()
{
    echo eval(
        "return
            'echo';"
                )
    . "foo";
}

testEvalExpressionHasExpectedEndColumn();