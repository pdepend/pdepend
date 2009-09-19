<?php
function testVariableDeclaratorHasExpectedStartColumn()
{
    static $a = 1,
           $b = array();
}
