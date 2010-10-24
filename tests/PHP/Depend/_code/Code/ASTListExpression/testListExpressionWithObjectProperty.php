<?php
function testListExpressionWithObjectProperty()
{
    list($obj->foo, $obj->bar) = func_num_args();
}