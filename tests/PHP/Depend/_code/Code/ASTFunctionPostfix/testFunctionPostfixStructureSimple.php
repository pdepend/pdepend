<?php
function testFunctionPostfixStructureSimple($i = 0)
{
    if ($i === 0) {
        testFunctionPostfixStructureSimple(1);
    }
}