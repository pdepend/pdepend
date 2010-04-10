<?php
function testCalculateCCNCountsAllCaseLabelsInSwitchStatement()
{
    switch ($foo) {
        case 13: break;
        case 23: break;
        case 42: break;
    }
}