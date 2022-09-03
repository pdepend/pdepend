<?php
readonly class FooBar
{
    public int $stuff;

    public function __construct(int $in)
    {
        $this->stuff = $in * 2;
    }
}
