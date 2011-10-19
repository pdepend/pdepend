<?php
function testGraphDereferencedArrayFromFunctionCallAndMultipleMethodInvocations()
{
    return foo(23)[42]->bar()[17]->baz()[0];
}
