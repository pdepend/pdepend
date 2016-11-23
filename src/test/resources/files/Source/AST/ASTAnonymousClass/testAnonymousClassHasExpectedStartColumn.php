<?php
function testAnonymousClassHasExpectedStartColumn()
{
    return new class(42, 23) {
        public function foo()
        {
            return self::class;
        }
    };
}
