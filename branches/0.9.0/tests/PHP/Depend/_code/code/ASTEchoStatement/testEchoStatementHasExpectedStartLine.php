<?php
function testEchoStatementHasExpectedStartLine($foo, $bar)
{
    echo $foo,
            ' World ',
                $bar->baz;
}
testEchoStatementHasExpectedStartLine('Hello', (object) array('!!!11'));