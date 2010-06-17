<?php
namespace nspace;

class Clazz
{
	function method()
    {
        throw new \InvalidArgumentException('Lorem ipsum...', 123);
	}
}

$obj = new Clazz();
$obj->method();