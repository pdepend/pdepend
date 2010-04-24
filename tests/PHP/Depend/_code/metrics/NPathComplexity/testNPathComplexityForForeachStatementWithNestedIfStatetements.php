<?php
function testNPathComplexityForForeachStatementWithNestedIfStatetements()
{
    foreach ($array as $e) {
        if ($e) {
            return $e;
        }
    }
}