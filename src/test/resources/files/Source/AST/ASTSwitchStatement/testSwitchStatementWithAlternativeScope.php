<?php
function testSwitchStatementWithAlternativeScope($value)
{
    switch ($value):
        case 42:
            if (time() % 23 === 0):
                echo 'A';
            elseif (time() % 42 === 0):
                echo 'B';
            else:
                echo 'C';
            endif;
            break;

        case 23:
            for ($i = 0; $i < $value; ++$i):
                echo $i, PHP_EOL;
            endfor;
            break;

        default:
            echo 'Nothing';
            break;

    endswitch;
}

testSwitchStatementWithAlternativeScope(17);
