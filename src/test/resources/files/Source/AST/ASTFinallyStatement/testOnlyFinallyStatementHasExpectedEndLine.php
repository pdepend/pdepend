<?php
function testFinallyStatementHasExpectedEndLine()
{
    try {
        throw Exception();
    } finally {
        echo "FOO";
    }
}
