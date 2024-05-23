<?php

class Foo {
    public function assertNotEmpty($value)
    {
        return $value ?: throw new \InvalidArgumentException('should not be empty');
    }
}
