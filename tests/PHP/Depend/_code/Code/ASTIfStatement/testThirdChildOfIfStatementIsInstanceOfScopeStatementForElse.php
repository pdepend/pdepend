<?php
function testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse($param)
{
    if ($param) {
        echo $param;
    } else {
        echo "NOOOOOO";
    }
}