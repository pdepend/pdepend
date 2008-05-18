<?php


/**
 * @package pkg1
 */
function foo($foo = array()) {
    foreach ($foo as $bar) {
        FooBar::y($bar);
    }
}
/**
 * @package pkg2
 */
interface Foo {
    function x();
}

/**
 * @package pkg3
 */
abstract class Bar implements Foo {
    private $foo = 17;
    protected abstract function y(Bar $bar);
}

/**
 * @package pkg1
 */
class FooBar extends Bar {
    private $x = 0x1234;
    protected $y = null;
    public $z = 'pdepend';
    
    public final function x() {}
    protected function y(Bar $bar) {
        if ($bar !== null) {
            $bar = new BarFoo($bar);
        }
    }   
}

/**
 * Enter description here...
 *
 * @return unknown
 */
function bar() {
    return 'bar';
}

$bar = new FooBar();
$bar->y();
?>