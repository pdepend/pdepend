<?php
function testParserHandlesStaticMemberExpressionSyntax()
{
    $x = "f";
    $y = "o";
    echo A::{$x.$y.$y}();
}

class A {
    static function foo() {
        return __METHOD__;
    }
}

testParserHandlesStaticMemberExpressionSyntax();
