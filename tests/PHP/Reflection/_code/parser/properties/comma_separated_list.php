<?php
class PHP_Reflection {
    /** Test comment $foo. */
    protected $foo = 0,
              // Test comment $bar
              $bar = null,
              /** Test comment $foobar */
              $foobar/* Ignore */,
              /* Test comment $barfoo */
              $barfoo # Ignore
              ;
              
    /** Test comment private. */
    private static $_a = 0, $_b = 1, $_c = 2;
}
?>