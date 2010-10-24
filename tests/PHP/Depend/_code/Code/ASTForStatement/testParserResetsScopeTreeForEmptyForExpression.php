<?php
/**
 * @package package
 */
class Clazz extends ParentClazz
{
    function m1() {
    }

    function m2() {
        for($i = 42;; ++$i)
            echo $i;
    }
}
