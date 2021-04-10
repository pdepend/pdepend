<?php
class Foo
{
    function testFunctionReturnTypeHintSelf(): self
    {
        return $this;
    }
}
