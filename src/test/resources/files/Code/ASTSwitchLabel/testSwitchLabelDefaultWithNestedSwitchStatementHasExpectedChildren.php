<?php
function testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren()
{
    switch ($foo)
    {
        default:
            switch ($bar) {
                case 1:
                    break;
                case 2:
                    break;
            }
            break;
    }
}