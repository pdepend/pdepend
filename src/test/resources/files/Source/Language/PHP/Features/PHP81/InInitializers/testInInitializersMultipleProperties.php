<?php
class Foo
{
    public function __construct(
        public Bar $bar = new Bar,
        protected string $stuff = 'abc',
        $biz = new Biz('lala'),
        private Smth $smth,
    ) {
    }
}

