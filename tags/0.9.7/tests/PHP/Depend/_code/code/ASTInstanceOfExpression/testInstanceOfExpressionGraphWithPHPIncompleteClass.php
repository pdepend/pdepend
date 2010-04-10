<?php
function testInstanceOfExpressionGraphWithPHPIncompleteClass($object)
{
    return ($object instanceof __PHP_Incomplete_Class);
}