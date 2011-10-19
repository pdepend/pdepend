<?php
function testGraphDereferencedArrayFromFunctionCallAndMethodInvocation()
{
    return foo()[0]->bar();
}
