<?php
function testSwitchLabelDefaultHasExpectedEndLine()
{
    switch ($foo)
    {
        default /* default */:
            break;

        case 42:
            break;
    }
}