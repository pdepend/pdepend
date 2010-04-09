<?php
class testGetAllMethodsContainsMethodsOfImplementedInterfaces
    implements testGetAllMethodsContainsMethodsOfImplementedInterfaceA,
               testGetAllMethodsContainsMethodsOfImplementedInterfaceB
{

}

interface testGetAllMethodsContainsMethodsOfImplementedInterfaceA
{
    function foo();
    function baz();
}

interface testGetAllMethodsContainsMethodsOfImplementedInterfaceB
{
    function bar();
}