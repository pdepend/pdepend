<?php
function testFinallyStatementHasExpectedStartLine()
{
    try {
        throw Exception();
    } catch (Exception $e) {

    } finally {
        echo "FOO";
    }
}
