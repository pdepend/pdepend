<?php

function testYieldWithFunctionCalls()
{
    yield foo(23) => bar(baz('tony', 'mono'));
}
