<?php
class z_testGetConstantsReturnsExpectedResultForInheritedConstantDefinitions
{
    const FOO = 42;
}

class testGetConstantsReturnsExpectedResultForInheritedConstantDefinitions
    extends z_testGetConstantsReturnsExpectedResultForInheritedConstantDefinitions
{
    const BAR = 23;
}
?>
