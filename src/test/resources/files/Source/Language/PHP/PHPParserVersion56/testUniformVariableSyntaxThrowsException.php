<?php
class UniformVariableSyntax
{
    static $bar, $baz;

    public function foo($foo, $bar, $baz)
    {
        echo $foo::$bar::$baz, PHP_EOL;
    }
}

$foo = new UniformVariableSyntax();
$foo::$bar = $foo;
$foo::$baz = 'foo::bar::baz';
$foo->foo($foo, 'bar', 'baz');
