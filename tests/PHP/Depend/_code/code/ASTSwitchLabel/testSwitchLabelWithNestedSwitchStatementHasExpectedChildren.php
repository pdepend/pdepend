<?php
function testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren()
{
    switch ($foo)
    {
        case (42 + 23):
            switch ($bar) {
                case 1:
                    break;
                case 2:
                    break;
            }
            break;
    }
}