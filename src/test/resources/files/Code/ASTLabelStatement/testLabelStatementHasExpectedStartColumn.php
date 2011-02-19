<?php
function testLabelStatementHasExpectedStartColumn()
{
    label_foo:

    echo "Hello World";
    if (time() % 42 === 0) {
        goto label_foo;
    }
}