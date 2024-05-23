<?php

namespace Baz;

class Foo
{
    public function bar($in) {
        return match($in) {
            'a' => 'A',
            'b' => 'B',
            default => throw new \InvalidArgumentException("Invalid code [$in]"),
        };
    }
}

