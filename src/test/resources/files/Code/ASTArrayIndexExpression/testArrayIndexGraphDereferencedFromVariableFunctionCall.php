<?php
function testArrayIndexGraphDereferencedFromVariableFunctionCall($function)
{
    return $function()[23];
}
