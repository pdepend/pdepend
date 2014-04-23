<?php
function testFinallyStatementHasExpectedEndColumn()
{
    try {
        throw Exception();
    } catch (Exception $e) {

    } finally {
        echo "FOO";
    }
}
