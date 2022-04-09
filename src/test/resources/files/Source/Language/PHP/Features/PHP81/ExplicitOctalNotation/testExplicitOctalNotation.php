<?php

class Foo
{
    private const THOUSAND = 1_000;
    private const HEXADECIMAL = 0xf;
    private const IMPLICIT_OCTAL = 016;
    private const EXPLICIT_OCTAL = 0o16;
    private const BINARY = 0b110;

    public function a()
    {
        return 0o170;
    }
}
