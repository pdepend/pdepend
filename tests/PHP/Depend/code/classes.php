<?php
/**
 * Foobar
 */
abstract class Foo {
    
}

class Bar extends Foo {
    function foo() {
        FooBar::bar();
        $bar = 'foo';
        $foo = true;
    }
}