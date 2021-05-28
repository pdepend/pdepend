<?php

class Foo {
    public function assertEmpty($value)
    {
        return $value ? throw new \InvalidArgumentException('should be empty') : $value;
    }
}
