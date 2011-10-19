<?php
class testGetParentClassesReturnsExpectedListClasses
    extends testGetParentClassesReturnsExpectedListClasses_parentA
{

}

class testGetParentClassesReturnsExpectedListClasses_parentA
    extends testGetParentClassesReturnsExpectedListClasses_parentB
{

}

class testGetParentClassesReturnsExpectedListClasses_parentB
    extends testGetParentClassesReturnsExpectedListClasses_parentC
{
    
}

class testGetParentClassesReturnsExpectedListClasses_parentC
{

}
