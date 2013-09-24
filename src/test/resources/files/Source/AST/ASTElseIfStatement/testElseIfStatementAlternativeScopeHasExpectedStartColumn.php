<?php
function testElseIfStatementAlternativeScopeHasExpectedStartColumn($value)
{
    if ($value === 42):
        echo 'Yes', PHP_EOL;
    elseif
        ($value < 42)
            :
        echo 'No', PHP_EOL;
    endif
        ;
}

testElseIfStatementAlternativeScopeHasExpectedStartColumn(23);