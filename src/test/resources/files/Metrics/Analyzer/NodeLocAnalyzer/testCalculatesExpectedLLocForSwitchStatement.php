<?php
function testCalculatesExpectedLLocForSwitchStatement($p)
{
    switch ($p) {
        case 42:
        case 23:
        case 17:
            break;

        default:
            break;
    }
}