<?php
function testGetImageForArrayIndexedVariableFunction(array $function)
{
    return $function[42](23);
}
