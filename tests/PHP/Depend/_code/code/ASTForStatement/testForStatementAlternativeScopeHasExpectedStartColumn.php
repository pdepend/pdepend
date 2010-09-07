<?php
function testForStatementAlternativeScopeHasExpectedStartColumn($count)
{
    for ($i = 0; $i < $count; ++$i):
        if ($i < 23):
            echo 'To low...', PHP_EOL;
        elseif ($i === 23):
            echo 'Yes...', PHP_EOL;
        else:
            echo 'To high...', PHP_EOL;
        endif;
    endfor;
}

testForStatementAlternativeScopeHasExpectedStartColumn(42);
