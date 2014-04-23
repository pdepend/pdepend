<?php
function testChildOfFinallyStatementIsScopeStatement()
{
    try {
        fooBar();
    } catch (Exception $e) {
        // Hello World
    } finally {
        echo "FOO";
    }
}
