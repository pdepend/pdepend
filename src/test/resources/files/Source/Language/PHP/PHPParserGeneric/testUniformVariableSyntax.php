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

class UniformVariableSyntaxStatic
{
    public $bar;

    public function __construct() {
        $this->bar = new X();
        $this->bar::$array = [1, 2];
    }

    public function validVariableName() {
        if ($this->bar::$array[1]) {
            var_dump($this->bar::$array);
        }
    }
}

class X {
    public static $array;
}

$o = new UniformVariableSyntaxStatic();
$o->validVariableName();

class UniformVariableSyntaxCallReturn
{
    public function create() {
        return function () {
            echo __METHOD__, PHP_EOL;
        };
    }
}

$o = new UniformVariableSyntaxCallReturn();
$o->create()();

class UniformVariableSyntaxOperatorOnExpression
{
    public function call() {
        (function () {
            echo __METHOD__, PHP_EOL;
        })();
    }
}

$o = new UniformVariableSyntaxOperatorOnExpression();
$o->call();

class UniformVariableSyntaxOperatorOnExpression
{
    public function call() {
        ($this)::go("Sindelfingen");
    }

    public static function go($query) {
        echo $query, PHP_EOL;
    }
}

