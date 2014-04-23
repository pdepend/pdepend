<?php
function testFinallyStatementHasExpectedEndLine()
{
    try {
        throw Exception();
    } catch (Exception $e) {

    } finally {
        echo "FOO";
    }
}
