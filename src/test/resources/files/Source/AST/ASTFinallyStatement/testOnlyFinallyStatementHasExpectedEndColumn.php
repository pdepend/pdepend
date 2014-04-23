<?php
function testFinallyStatementHasExpectedEndColumn()
{
    try {
        throw Exception();
    } finally {
        echo "FOO";
    }
}
