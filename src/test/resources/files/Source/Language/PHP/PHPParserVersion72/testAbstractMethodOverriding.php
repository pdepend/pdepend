<?php

abstract class A
{
    abstract function test(string $s);
}

abstract class B extends A
{
    abstract function test($s) : int;
}
