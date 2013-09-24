<?php
function testSwitchLabelDefault( $foo )
{
    switch ($foo)
    {
        default /* default */:
            break;

        case 42:
            break;
    }
}
