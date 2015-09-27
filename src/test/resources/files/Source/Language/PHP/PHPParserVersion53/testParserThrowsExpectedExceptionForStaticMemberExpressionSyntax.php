<?php
function testParserThrowsExpectedExceptionForStaticMemberExpressionSyntax()
{
    $x = "f";
    $y = "o";
    A::{$x.$y.$y}();
}
