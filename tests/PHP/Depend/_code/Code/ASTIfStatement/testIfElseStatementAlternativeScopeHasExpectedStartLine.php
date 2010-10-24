<?php
function testIfElseStatementAlternativeScopeHasExpectedStartLine($value)
{
    if ($value === 42):
        echo 'TRUE', PHP_EOL;
    else:
        echo 'FALSE', PHP_EOL;
    endif;
}

testIfElseStatementAlternativeScopeHasExpectedStartLine(42);