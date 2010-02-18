<?php
function testUnclosedSwitchStatementResultsInExpectedException()
{
    switch ($x) {
        case 1:
            break;
}