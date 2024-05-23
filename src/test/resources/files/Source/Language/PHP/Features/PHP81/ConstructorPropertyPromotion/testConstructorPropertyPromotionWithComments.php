<?php
class Foo
{
    public function __construct(
        /* comment here */
        private $bar,
        protected /** comment there */int $biz,
        public ?Stringable $str = null, // here again
        array $options = [],
    ) {
    }
}
