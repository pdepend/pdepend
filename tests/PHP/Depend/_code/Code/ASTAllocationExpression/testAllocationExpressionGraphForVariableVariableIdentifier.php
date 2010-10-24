<?php
function testAllocationExpressionGraphForVariableVariableIdentifier()
{
    new $$foo;
}