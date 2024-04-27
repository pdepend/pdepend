<?php
class Foo
{
    public function bar()
    {
        return new Route('/thing/{id}', name: 'get_thing_by_id', requirements: ["id" => "\d+"], methods: ['GET']);
    }
}
