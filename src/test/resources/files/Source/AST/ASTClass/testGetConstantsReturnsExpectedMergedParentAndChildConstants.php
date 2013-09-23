<?php
class testGetConstantsReturnsExpectedMergedParentAndChildConstants
    extends testGetConstantsReturnsExpectedMergedParentAndChildConstants_parent
{
    const FOO = 42;
}

class testGetConstantsReturnsExpectedMergedParentAndChildConstants_parent
{
    const FOO = 17,
          BAR = 23;
}