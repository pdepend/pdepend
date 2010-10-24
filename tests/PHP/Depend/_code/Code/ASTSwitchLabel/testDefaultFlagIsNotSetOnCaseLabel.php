<?php
function testDefaultFlagIsNotSetOnCaseLabel()
{
    switch ($foo)
    {
        case 42:
            break;
        
        default:
            break;
    }
}