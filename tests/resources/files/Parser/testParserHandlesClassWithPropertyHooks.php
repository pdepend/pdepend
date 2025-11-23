<?php

class testParserHandlesClassWithPropertyHooks
{
    public string $foo {
        get {
            return 'foo:' . $this->data;
        }
        set {
            $this->data = $value;
        }
    }
}
