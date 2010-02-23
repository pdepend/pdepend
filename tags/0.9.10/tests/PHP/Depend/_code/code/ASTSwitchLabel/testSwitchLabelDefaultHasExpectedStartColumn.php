<?php
function testSwitchLabelDefaultHasExpectedStartColumn()
{
    switch ($foo)
    {
        default /* default */:
            break;

        case 42:
            break;
    }
}