<?php
function testContinueStatementHasExpectedStartLine()
{
    for ($i = 0; $i < 42; ++$i) {
        continue
            2
                ;
    }
}