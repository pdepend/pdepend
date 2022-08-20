<?php
class Foo
{
    public function bar($className, $method)
    {
        return $className::$method(...);
    }
}
