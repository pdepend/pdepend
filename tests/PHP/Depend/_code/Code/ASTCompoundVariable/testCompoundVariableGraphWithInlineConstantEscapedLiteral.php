<?php
function testCompountVariableGraphWithInlineConstantEscapedLiteral()
{
    ${'FOO{$bar}'};
}