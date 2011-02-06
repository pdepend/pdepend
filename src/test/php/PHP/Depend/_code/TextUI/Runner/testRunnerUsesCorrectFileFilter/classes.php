<?php
/**
 * Foobar
 */
abstract class Foo {
    
}

class Bar extends Foo {
    public function foo() {
        FooBar::bar();
        $bar = 'foo';
        $foo = true;
    }
}