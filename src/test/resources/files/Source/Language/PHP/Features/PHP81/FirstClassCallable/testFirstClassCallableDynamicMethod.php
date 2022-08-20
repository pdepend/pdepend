<?php
class Foo
{
    public function bar($obj, $method)
    {
        return $obj->$method(...);
    }
}
