<?php

class testSetVisibility
{
    protected(set) string $foo {
        set {
            $this->foo = $value;
        }
    }
}
