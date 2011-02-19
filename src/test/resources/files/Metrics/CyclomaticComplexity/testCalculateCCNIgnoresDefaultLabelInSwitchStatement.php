<?php
function testCalculateCCNIgnoresDefaultLabelInSwitchStatement()
{
    switch ($foo) {
        case 23: break;
        case 42: break;
        default: break;
    }
}