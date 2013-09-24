<?php
function testGraphDereferencedArrayFromVariableClassStaticMethodCallAndMultipleMethodInvocations($class)
{
    var_dump($class::foo(23)[42]->bar()[17]->baz()[0]);
}
