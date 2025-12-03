<?php

class testGetMethod
{
    public string $foo {
        get {
            return 'foo:' . $this->foo;
        }
    }
}
