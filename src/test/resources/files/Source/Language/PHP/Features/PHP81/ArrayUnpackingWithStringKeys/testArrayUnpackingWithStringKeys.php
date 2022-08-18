<?php

class Foo
{
    public function bar(): array
    {
        return [...['a' => 'foo'], ...['b' => 'bar']];
    }
}
