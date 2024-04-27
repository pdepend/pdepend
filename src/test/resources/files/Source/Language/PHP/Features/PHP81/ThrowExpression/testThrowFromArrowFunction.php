<?php

class Foo {
    public function returnThrowCallable()
    {
        return fn () => throw new \BadMethodCallException('not implemented');
    }
}
