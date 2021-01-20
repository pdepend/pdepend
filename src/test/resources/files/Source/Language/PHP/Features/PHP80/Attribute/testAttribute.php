<?php

#[Attribute]
class Foo
{

}

class A
{
    public function b(#[Foo()] $bar)
    {

    }
}
