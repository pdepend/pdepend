<?php
function testSwitchLabelHasExpectedStartLine()
{
    switch ($foo)
    {
        case 42:
            break;

        default:
            break;
    }
}