<?php

#[Attribute1]
class Foo
{

}

#[Attribute2]
#[Attribute3]
class A
{
    #[Prop]
    public int $prop;

    public function __construct(#[Autowire] protected InjectedClass $dependency)
    {

    }

    #[Route('/thing/{id}', name: 'get_thing_by_id', requirements: ["id" => "\d+"], methods: ['GET'])]
    public function getById(Request $request): Response
    {
        // ...
    }

    #[Bar([']][[]]'])]
    public function bar()
    {
        // ...
    }

    #[Foo, Bar]
    public function foobar()
    {
        // ...
    }

    public function b(#[Foo()] $bar)
    {

    }
}

#[FunBar]
function funbar(): void {
}
