<?php
function testFourthChildOfForStatementIsInstanceOfStatement()
{
    for ($i = 0, $j = 0; $i < $j; ++$i, --$j)
        break 42;
}