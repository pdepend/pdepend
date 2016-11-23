<?php
function testAnonymousClassHasExpectedStartColumn()
{
    return new class(42, new \ArrayIterator([])) {
        public function foo()
        {
            return self::class;
        }
    };
}
