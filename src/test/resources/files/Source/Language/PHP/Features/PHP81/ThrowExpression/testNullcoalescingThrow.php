<?php

class Foo {
    public function assertNotNull($value)
    {
        return $value ?? throw new \InvalidArgumentException('should not be null');
    }
}
