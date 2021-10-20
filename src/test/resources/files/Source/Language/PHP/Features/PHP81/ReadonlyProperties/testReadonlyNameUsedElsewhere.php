<?php
class Foo
{
    public string $readonly;

    public function __construct()
    {
        $this->readonly = 'foo';
    }

    public function readonly($readonly)
    {

    }
}
