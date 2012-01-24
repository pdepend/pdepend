<?php
if (!function_exists('foo')) {
    function foo() {}
}

/**
 * @package my.package
 */
class testParserExtractsCorrectClassPackage {}
