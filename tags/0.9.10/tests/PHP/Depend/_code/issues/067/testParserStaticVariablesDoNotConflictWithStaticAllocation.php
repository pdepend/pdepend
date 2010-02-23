<?php
class Foo
{
    public function test()
    {
        static $x = true;
        static $y = false;
        return new static();
    }
}
?>
