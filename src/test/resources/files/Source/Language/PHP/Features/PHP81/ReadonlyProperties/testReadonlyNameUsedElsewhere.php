<?php
class Foo
{
    const readonly = 'baz';

    public readonly string $readonly;

    readonly public string $readonly2;

    public function __construct()
    {
        $this->readonly = $this->readonly();
        $this->readonly2 = $this->readonly();
    }

    public function readonly()
    {
        return self::readonly;
    }
}
