<?php
interface ParentClass
{
    function test(string $param) : ?string;
}

interface ChildClass2 extends ParentClass
{
    function test(string $param) : ?string;
}
