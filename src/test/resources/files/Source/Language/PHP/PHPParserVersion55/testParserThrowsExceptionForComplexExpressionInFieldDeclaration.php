<?php
class testParserThrowsExceptionForComplexExpressionInFieldDeclaration
{
    public $bar1 = 1+2-3*4/5 + self::BAR;
    public $bar2 = 'hello ' . 'world';
}
