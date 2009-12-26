<?php
function testDefaultFlagIsSetOnDefaultLabel()
{
    switch ($foo)
    {
        default:
            break;

        case 42:
            break;
    }
}