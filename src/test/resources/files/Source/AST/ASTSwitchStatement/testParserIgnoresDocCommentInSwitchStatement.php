<?php
function testParserIgnoresDocCommentInSwitchStatement($bar)
{
    switch ($bar)
    {
        /**
         * Hello
         */
        case 1:
        case 2:
            break;

        /**
         * World
         */
        case 3:
        case 4:
        case 5:
            break;
    }
}