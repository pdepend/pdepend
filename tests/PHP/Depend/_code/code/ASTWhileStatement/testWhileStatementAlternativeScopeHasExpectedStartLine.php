<?php
function testWhileStatementAlternativeScopeHasExpectedStartLine(array $values)
{
    while ($value = array_pop($values)):
        if ($value < 42):
            echo $value, PHP_EOL;
        endif;
    endwhile;
}

testWhileStatementAlternativeScopeHasExpectedStartLine(rand(23, 64));