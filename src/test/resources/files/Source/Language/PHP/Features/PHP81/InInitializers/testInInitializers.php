<?php
class Foo
{
    public function __construct(
        public Bar $bar = new Bar,
    ) {
    }
}

