<?php
class Foo
{
    public function bar($in) {
        return match($in) {
            'a', 'b' => 'AB',
            1 => 'One',
            default => throw new \InvalidArgumentException("Invalid code [$in]"),
        };
    }
}
