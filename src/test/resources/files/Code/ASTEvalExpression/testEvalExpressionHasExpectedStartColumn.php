<?php
function testEvalExpressionHasExpectedStartColumn()
{
    echo eval(
        "return
            'echo';"
                )
    . "foo";
}

testEvalExpressionHasExpectedStartColumn();