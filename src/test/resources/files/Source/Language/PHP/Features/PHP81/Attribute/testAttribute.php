<?php

#[Attribute]
class Foo
{

}

class A
{
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

    public function b(#[Foo()] $bar)
    {

    }
}
