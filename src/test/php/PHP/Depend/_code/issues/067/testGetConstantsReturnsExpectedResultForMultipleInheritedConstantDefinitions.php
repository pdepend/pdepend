<?php
class z_testGetConstantsReturnsExpectedResultForMultipleInheritedConstantDefinitions
{
    const BAZ = 11,
          F00 = 7;
}

class y_testGetConstantsReturnsExpectedResultForMultipleInheritedConstantDefinitions
    extends z_testGetConstantsReturnsExpectedResultForMultipleInheritedConstantDefinitions
{
    const FOO = 17;
    const BAZ = 13;
}

class testGetConstantsReturnsExpectedResultForMultipleInheritedConstantDefinitions
    extends y_testGetConstantsReturnsExpectedResultForMultipleInheritedConstantDefinitions
{
    const FOO = 42,
          BAR = 23;
}
?>
