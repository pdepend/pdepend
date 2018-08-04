<?php

class Foo
{
    public function bar()
    {
        echo 'baz';
    }
}

class FooFactory
{
    public function __invoke()
    {
        return new Foo();
    }
}

(new FooFactory())()->bar();
