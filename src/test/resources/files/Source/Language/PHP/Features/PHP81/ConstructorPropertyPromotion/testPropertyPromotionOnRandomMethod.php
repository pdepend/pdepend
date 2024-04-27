<?php
class Foo
{
    public function notAConstructor(
        private $bar,
        protected int $biz,
        public ?Stringable $str = null,
        array $options = []
    ) {
    }
}
