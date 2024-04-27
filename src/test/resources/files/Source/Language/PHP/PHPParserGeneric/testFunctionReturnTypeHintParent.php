<?php
class Foo extends Bar
{
    function testFunctionReturnTypeHintParent(): parent
    {
        return new Bar();
    }
}
