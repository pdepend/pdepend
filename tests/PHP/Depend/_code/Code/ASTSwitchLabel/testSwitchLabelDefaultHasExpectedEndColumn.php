<?php
function testSwitchLabelDefaultHasExpectedEndColumn()
{
    switch ($foo)
    {
        default /* default */:
            break;

        case 42:
            break;
    }
}