<?php

class Foo {
    public function returnThrowCallable()
    {
        $this->callAll(
            fn ($value) => $value ?? throw new \InvalidArgumentException('should not be null'),
            fn ($value) => $value ?: throw new \InvalidArgumentException('should not be empty'),
        );
    }

    public function callAll(callable ...$callables): void
    {
        array_map(
            fn (callable $callable) => $callable($this),
            $callables
        );
    }
}
