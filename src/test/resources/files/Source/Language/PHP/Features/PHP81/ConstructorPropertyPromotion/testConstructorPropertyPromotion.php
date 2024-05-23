<?php
class Foo
{
    public function __construct(
        private $bar,
        protected int $biz,
        public ?Stringable $str = null,
        array $options = []
    ) {
    }
}
