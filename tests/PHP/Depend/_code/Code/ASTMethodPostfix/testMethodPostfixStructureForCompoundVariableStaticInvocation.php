<?php
function testMethodPostfixStructureForCompoundVariableStaticInvocation()
{
    Bar::${BAZ}();
}