<?php
class testGetAllMethodsContainsMethodsOfImplementedInterface
    implements GetAllMethodsContainsMethodsOfImplementedInterface
{

}

interface GetAllMethodsContainsMethodsOfImplementedInterface
{
    function foo();
    function bar();
    function baz();
}