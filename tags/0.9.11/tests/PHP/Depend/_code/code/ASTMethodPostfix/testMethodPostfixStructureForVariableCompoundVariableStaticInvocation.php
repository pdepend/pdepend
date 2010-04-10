<?php
function testMethodPostfixStructureForVariableCompoundVariableStaticInvocation()
{
    Bar::$${BAZ}();
}