<?php
interface x_testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions
{
    const FOO = 42;
    const B4R = 13;
}

class y_testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions
    implements x_testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions

{
    const F00 = 37;
    const BAZ = 11;
}

interface z_testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions
{
    const BAR = 5;
}

class testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions
       extends y_testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions
    implements z_testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions
{
    const BAZ = 7;
    const FOO = 3;
}

?>
