<?php
function testCompountVariableGraphWithInlineLiteral()
{
    ${"FOO{$bar}"};
}