<?php
function testSwitchLabelDefaultHasExpectedStartLine()
{
    switch ($foo)
    {
        default /* default */:
            break;

        case 42:
            break;
    }
}