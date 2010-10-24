<?php
function testThirdChildOfCatchStatementIsScopeStatement()
{
    try {
        fooBar();
    } catch (Exception $e) {
        // Hello World
    }
}