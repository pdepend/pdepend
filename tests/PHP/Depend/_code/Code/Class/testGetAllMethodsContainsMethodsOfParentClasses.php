<?php
class testGetAllMethodsContainsMethodsOfParentClasses
    extends testGetAllMethodsContainsMethodsOfParentClassA
{
    function foo() {}
}

class testGetAllMethodsContainsMethodsOfParentClassA
    extends testGetAllMethodsContainsMethodsOfParentClassB
{
    function bar() {}
}

class testGetAllMethodsContainsMethodsOfParentClassB
{
    function baz() {}
}