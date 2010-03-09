<?php
function testCalculateCCNWithConditionalExprInCompoundExpr($foo, $bar = false)
{
    return $foo->{$bar ? 'bar' : 'baz'};
}