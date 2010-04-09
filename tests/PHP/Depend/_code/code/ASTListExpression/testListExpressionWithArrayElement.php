<?php
function testListExpressionWithArrayElement()
{
    list($array['arg1'], $array['arg2']) = func_get_args();
}