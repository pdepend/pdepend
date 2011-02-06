<?php
function testEchoStatementHasExpectedEndLine($foo, $bar)
{
    echo $foo,
            ' World ',
                $bar->baz;
}
testEchoStatementHasExpectedEndLine('Hello', (object) array('!!!11'));