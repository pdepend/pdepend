<?php
class Foo
{
    public function bar($in) {
        return match($in, 6) {
            'a' => 'A',
            'b' => 'B',
            default => throw new \InvalidArgumentException("Invalid code [$in]"),
        };
    }
}
