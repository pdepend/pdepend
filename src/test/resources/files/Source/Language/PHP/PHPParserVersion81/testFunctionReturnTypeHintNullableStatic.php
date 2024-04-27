<?php
class Foo
{
    function testFunctionReturnTypeHintNullableStatic(): ?static
    {
        return new static();
    }
}
