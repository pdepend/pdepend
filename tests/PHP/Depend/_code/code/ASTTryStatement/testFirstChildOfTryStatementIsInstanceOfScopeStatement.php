<?php
function testFirstChildOfTypeStatementIsInstanceOfScopeStatement()
{
    try {
        fooBar();
    } catch (Exception $e) {
        // Silent
    }
}