<?php
function testFinallyStatementHasExpectedStartLine()
{
    try {
        throw Exception();
    } finally {
        echo "FOO";
    }
}
