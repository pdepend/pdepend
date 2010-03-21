<?php
/**
 * This is a file comment.
 */

/**
 * Sample comment.
 */
class Foo {
   /**
    * Method comment.
    */ 
   function bar() {}
}

interface BarI {}

class Bar implements BarI {
    /**
     * Method comment.
     */
    function foo() {}
}

/**
 * A second comment...
 */
interface FooI extends BarI {}