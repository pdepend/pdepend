<?php
function testGetStaticVariablesReturnsMergeOfAllStaticVariables()
{
    static $a = 42;
    static $b = 23,
           $c = 17;
}