<?php
class Foo
{
    function testFunctionReturnTypeHintStatic(): static
    {
        return new static();
    }
}
