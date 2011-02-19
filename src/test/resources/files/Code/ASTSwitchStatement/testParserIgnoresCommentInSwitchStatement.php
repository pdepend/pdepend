<?php
function testParserIgnoresCommentInSwitchStatement($foo)
{
    switch ($foo)
    {
        case 1:
        case 2:
            break;

        // Hello
        case 3:
            break;

        # World
        case 4:
            break;
    }
}