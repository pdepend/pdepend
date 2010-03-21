<?php
class testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces
    implements testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaceA
{

}

interface testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaceA
    extends testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaceB
{
    function foo();
}

interface testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaceB
{
    function bar();
    function baz();
}