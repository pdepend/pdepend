<?php
function testGetStaticVariablesReturnsFirstSetOfStaticVariables()
{
    static $a = 42, $b = 23;
}