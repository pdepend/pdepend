<?php
function testGraphDereferencedArrayFromStaticMethodCallAndMultipleMethodInvocations()
{
    return Clazz::foo(23)[42]->bar()[17]->baz()[0];
}
