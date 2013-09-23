<?php
interface testGetParentClassesReturnsEmptyArray
    extends testGetParentClassesReturnsEmptyArray_parentA,
            testGetParentClassesReturnsEmptyArray_parentB
{

}

interface testGetParentClassesReturnsEmptyArray_parentA
    extends testGetParentClassesReturnsEmptyArray_parentC
{

}

interface testGetParentClassesReturnsEmptyArray_parentB
{

}

interface testGetParentClassesReturnsEmptyArray_parentC
{
    
}
