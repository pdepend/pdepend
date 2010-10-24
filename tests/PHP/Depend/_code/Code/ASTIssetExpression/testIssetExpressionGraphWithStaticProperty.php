<?php
function testIssetExpressionGraphWithStaticProperty()
{
    return isset(FooBar::$baz);
}