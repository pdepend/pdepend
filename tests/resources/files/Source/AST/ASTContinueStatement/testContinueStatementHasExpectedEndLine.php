<?php
function testContinueStatementHasExpectedEndLine()
{
    while (true) {
        for ($i = 0; $i < 42; ++$i) {
            continue
                2
                    ;
        }
    }
}
