<?php
function testEchoStatementHasExpectedStartColumn($foo, $bar)
{
    echo $foo,
            ' World ',
                $bar->baz;
}
testEchoStatementHasExpectedStartColumn('Hello', (object) array('!!!11'));