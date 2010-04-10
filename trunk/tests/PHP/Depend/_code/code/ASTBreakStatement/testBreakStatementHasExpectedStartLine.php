<?php
function testBreakStatementHasExpectedStartLine()
{
    for ($i = 0; $i < 42; ++$i) {
        break
            2
                ;
    }
}