<?php
class testPropertyPostfixParentVariableInClassWithoutParentThrowsExpectedException
{
    function testPropertyPostfixParentVariableInClassWithoutParentThrowsExpectedException()
    {
        parent::$bar;
    }
}