<?php
function testFinallyStatementHasExpectedStartColumn()
{
    try {
        throw Exception();
    } catch (Exception $e) {

    } finally {
        echo "FOO";
    }
}
