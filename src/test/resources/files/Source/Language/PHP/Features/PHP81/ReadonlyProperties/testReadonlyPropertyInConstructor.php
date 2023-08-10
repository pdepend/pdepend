<?php
class Foo
{
    public function __construct(
        public readonly string $bar,
        readonly public int|float $foo
    ) {}
}
