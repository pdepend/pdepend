<?php
class Foo
{
    const readonly = 'baz';

    public readonly string $readonly;

    public function __construct()
    {
        $this->readonly = $this->readonly();
    }

    public function readonly()
    {
        return self::readonly;
    }
}
