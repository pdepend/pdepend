<?php
class testGetConstantsReturnsExpectedInterfaceConstants
    implements testGetConstantsReturnsExpectedInterfaceConstantsInterface
{
    const FOO = 42;
}

interface testGetConstantsReturnsExpectedInterfaceConstantsInterface
{
    const BAR = 23;
}
