<?php
function testEchoStatementHasExpectedEndColumn($foo, $bar)
{
    echo $foo,
            ' World ',
                $bar->baz;
}
testEchoStatementHasExpectedEndColumn('Hello', (object) array('!!!11'));