<?php
function testSwitchStatementGraphWithBooleanExpressions()
{
    switch ($x && $y || $z) {
        case true: break;
    }
}