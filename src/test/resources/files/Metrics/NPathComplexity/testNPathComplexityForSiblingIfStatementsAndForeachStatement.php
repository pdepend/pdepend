<?php
function testNPathComplexityForSiblingIfStatementsAndForeachStatement()
{
    if (true) throw new Exception();
    if (true) return FALSE;
    foreach ($array as $e) {
        if ($e) {
            return $e;
        }
    }
    return FALSE;
}