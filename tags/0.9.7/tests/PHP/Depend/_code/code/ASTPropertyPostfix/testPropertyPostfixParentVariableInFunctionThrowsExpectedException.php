<?php
function testPropertyPostfixParentVariableInFunctionThrowsExpectedException()
{
    parent::$bar;
}