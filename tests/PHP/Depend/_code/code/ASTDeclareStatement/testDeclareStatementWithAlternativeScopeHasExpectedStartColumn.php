<?php
function testDeclareStatementWithAlternativeScopeHasExpectedStartColumn(array $values)
{
    declare (ticks=23):
        foreach ($values as $value)
        {
            echo ($value * $value), PHP_EOL;
        }
    enddeclare;
}

testDeclareStatementWithAlternativeScopeHasExpectedStartColumn(array(1, 2, 3, 4, 5, 6, 7));