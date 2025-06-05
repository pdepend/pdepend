<?php

class testParserHandlesClassWithPropertyHooks
{
    public private(set) string $data;
    public string $foo {
        get {
            return 'foo:' . $this->data;
        }
        set {
            $this->data = $value;
        }
    }
}
