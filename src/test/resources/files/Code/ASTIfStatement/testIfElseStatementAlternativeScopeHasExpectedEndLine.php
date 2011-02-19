<?php
function testIfElseStatementAlternativeScopeHasExpectedEndLine($value)
{
    if ($value === 42):
        echo 'TRUE', PHP_EOL;
    else:
        echo 'FALSE', PHP_EOL;
    endif;
}

testIfElseStatementAlternativeScopeHasExpectedEndLine(42);