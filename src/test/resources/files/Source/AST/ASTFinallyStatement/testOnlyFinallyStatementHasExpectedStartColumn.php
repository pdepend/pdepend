<?php
function testFinallyStatementHasExpectedStartColumn()
{
    try {
        throw Exception();
    } finally {
        echo "FOO";
    }
}
