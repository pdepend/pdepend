<?php
function testIssetExpressionHasExpectedEndLine()
{
    if (isset($GLOBALS['foo'])) {
        return 'T_FOO';
    }
    return 'T_NIL';
}