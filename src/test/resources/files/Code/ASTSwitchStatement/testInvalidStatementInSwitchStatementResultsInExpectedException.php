<?php
function testInvalidStatementInSwitchStatementResultsInExpectedException()
{
    switch ($foo) {
        break;
        case 42:
        break;
    }
}