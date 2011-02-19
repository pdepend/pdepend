<?php
/**
 * @package pkg1
 */
function foo($foo = array()) {
    foreach ($foo as $bar) {
        FooBar::y($bar);
    }
}
?>
