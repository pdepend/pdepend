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

    public function __construct(
        #[Promo1]
        protected int $name,
        #[Promo2]
        #[Promo3]
        protected int $count,
        #[Promo4]
        /** Trailing comment */
        protected array $list,
    ) {

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

    #[
        Foo,
        Bar,
    ]
    public function foobar2()
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

if (rand()) {
    #[Foo(Bar::class)]
    class B
    {
    }
}