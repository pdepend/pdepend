<?php
function testWhileStatementWithAlternativeScope(array $values)
{
    while ($value = array_pop($values)):
        if ($value < 42):
            echo $value, PHP_EOL;
        endif;
    endwhile;
}

testWhileStatementWithAlternativeScope(rand(23, 64));
