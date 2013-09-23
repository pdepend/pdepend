<?php
class testGetAllMethodsContainsMethodsOfParentClass
    extends testGetAllMethodsContainsMethodsOfParentClassA
{
    function foo() {}
}

class testGetAllMethodsContainsMethodsOfParentClassA
{
    function bar() {}
    function baz() {}
}