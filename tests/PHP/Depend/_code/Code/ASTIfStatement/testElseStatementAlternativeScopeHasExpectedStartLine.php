<?php
function testElseStatementAlternativeScopeHasExpectedStartLine($value)
{
    if ($value === 42):
        echo 'TRUE', PHP_EOL;
        else
            :
        echo 'FALSE', PHP_EOL;
    endif
                ;
}

testElseStatementAlternativeScopeHasExpectedStartLine(42);