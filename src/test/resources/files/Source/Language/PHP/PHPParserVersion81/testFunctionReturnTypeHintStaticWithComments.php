<?php
class Foo
{
    function testFunctionReturnTypeHintStaticWithComments(): ?/* comment */static
    {
        return new static();
    }
}
