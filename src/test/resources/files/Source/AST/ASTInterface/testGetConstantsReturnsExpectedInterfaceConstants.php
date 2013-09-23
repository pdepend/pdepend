<?php
interface testGetConstantsReturnsExpectedInterfaceConstants
    extends testGetConstantsReturnsExpectedInterfaceConstantsInterface
{
    const FOO = 42;
}

interface testGetConstantsReturnsExpectedInterfaceConstantsInterface
{
    const BAR = 23;
}
